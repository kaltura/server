<?php

/**
 *  @package server-infra
 *  @subpackage cache
 */
 class kQueryCacheKey
{
	static protected $exists = false;
	
	protected $key;
	
	public function __construct($key)
	{
		if (self::$exists)
			KalturaLog::crit('Unexpected - query cache key already exists');
		self::$exists = true;
		$this->key = $key; 
	}
	
	public function __destruct()
	{
		self::$exists = false;
	}
	
	public function getKey()
	{
		return $this->key;
	}
	
	static public function exists()
	{
		return self::$exists;
	} 
}

class kQueryCache 
{
	const CLOCK_SYNC_TIME_MARGIN_SEC = 10;			// When comparing the invalidation key timestamp to the query timestamp, 
													// the query timestamp should be greater by this value to use the cache
													// in order to compensate for clock differences
	const SLAVE_LAG_TIME_MARGIN_SEC = 70;			// This value is added to the measured slave lag as a safety margin.
													// it is composed of the lag measuring period (60) and the clock sync margin (10)
	const MAX_QUERY_MASTER_TIME_MARGIN_SEC = 300;	// The maximum time frame after a DB change during which we should query the master
	
	const MAX_CACHED_OBJECT_COUNT = 500;			// Select queries that return more objects than this const will not be cached
													// 500 serialized entries take 110K after compression, well below the memcache 1M limit  
	const CACHED_QUERIES_EXPIRY_SEC = 86400;		// The expiry of the query keys in the memcache 	
	const INVALIDATION_KEYS_EXPIRY_MARGIN = 3600;	// An extra expiry time given to invalidation keys over cached queries

	const MAX_IN_CRITERION_INVALIDATION_KEYS = 50;	// Maximum number of allowed elements in 'IN' to use the query cache
	
	const CACHE_PREFIX_QUERY = 'QCQ-';				// = Query Cache - Query
	const CACHE_PREFIX_INVALIDATION_KEY = 'QCI-';	// = Query Cache - Invalidation key
	const DONT_CACHE_KEY = 'QCC-DontCache';			// when set new queries won't be cached in the memcache
	const MAX_SLAVE_LAG_KEY = 'QCC-MaxSlaveLag';	// the maximum lag of slaves in the current DC
	
	const QUERY_TYPE_SELECT = 'sel-';
	const QUERY_TYPE_COUNT =  'cnt-';
	
	const QUERY_DB_UNDEFINED = 0;
	const QUERY_DB_MASTER = 1;
	const QUERY_DB_SLAVE = 2;
	
	const CACHE_VERSION = '2';

	const SPHINX_LAG_KEY = 'QCCSPH-SphinxLag';	// the lags of the different sphinx servers in the current DC

	protected static $s_memcacheKeys = null;
	protected static $s_memcacheQueries = null;
	protected static $s_memcacheInited = false;
	
	protected static function initGlobalMemcache()
	{
		if (self::$s_memcacheInited)
		{
			return;
		}
		
		self::$s_memcacheInited = true;
		
		self::$s_memcacheKeys = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_QUERY_CACHE_KEYS);
		if (self::$s_memcacheKeys === null)
		{
			// no reason to init the queries server, the query cache won't be used anyway
			return;
		}

		self::$s_memcacheQueries = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_QUERY_CACHE_QUERIES);
	}
	
	public static function close()
	{
		self::$s_memcacheInited = false;
		self::$s_memcacheKeys = null;
		self::$s_memcacheQueries = null;
	}

	protected static function replaceVariable($formatString, $variableValue)
	{
		$firstVarPos = strpos($formatString, '%s');
		if ($firstVarPos === false)
		{
			return $formatString;
		}
		
		return substr_replace($formatString, $variableValue, $firstVarPos, 2);
	}
	
	public static function getCriterionValues($criterion, $columnName)
	{
		// get current criterion values
		if ($criterion->getComparison() == Criteria::EQUAL)
		{
			$result = array($criterion->getValue());
		}
		else if ($criterion->getComparison() == Criteria::IN && 
			is_array($criterion->getValue()) &&
			count($criterion->getValue()) < self::MAX_IN_CRITERION_INVALIDATION_KEYS)
		{
			$result = $criterion->getValue();
		}
		else
		{
			return null;
		}
		
		// get child clause values
		if (in_array(Criterion::ODER, $criterion->getConjunctions()))
		{
			$childClauses = $criterion->getClauses();
			if (count($childClauses) != 1)
			{
				return null;			// we currently support a single OR child
			}
			
			$childClause = reset($childClauses);
			$childColumn = $childClause->getTable() . "." . $childClause->getColumn();
			if ($childColumn != $columnName)
			{
				return null;			// child clause is on a different column
			}
			
			$childValues = self::getCriterionValues($childClause, $columnName);
			if ($childValues === null)
			{
				return null;			// failed to get child values
			}
			
			$result = array_merge($result, $childValues);
		}
		
		return array_unique($result);		
	}

	protected static function getInvalidationKeysForQuery($invalidationKeyRules, Criteria $criteria)
	{
		foreach ($invalidationKeyRules as $invalidationKeyRule)
		{
			$invalidationKeys = array($invalidationKeyRule[0]);		// first element is the format string
			for ($colIndex = 1; $colIndex < count($invalidationKeyRule); $colIndex++)
			{
				$columnName = $invalidationKeyRule[$colIndex];
				$criterion = $criteria->getCriterion($columnName);
				if (!$criterion)
				{
					$invalidationKeys = null;
					break;
				}
				
				$values = self::getCriterionValues($criterion, $columnName);
				if ($values === null)
				{
					$invalidationKeys = null;
					break;
				}
				
				$newInvalidationKeys = array(); 
				foreach ($invalidationKeys as $invalidationKey)
				{
					foreach ($values as $value)
					{
						$value = strtolower(str_replace(' ', '_', $value));
						$newInvalidationKeys[] = self::replaceVariable($invalidationKey, $value);
					}
				}
				$invalidationKeys = $newInvalidationKeys;
			}
			
			if (!is_null($invalidationKeys))
			{
				return $invalidationKeys;
			}
		}
			
		return array();
	}

	public static function getCachedQueryResults(Criteria $criteria, $queryType, $peerClassName, &$cacheKey, &$queryDB)
	{
		if (!kConf::get("query_cache_enabled"))
		{
			return null;
		}
		
		// if the criteria has an empty IN, no need to go to the DB or memcache - return an empty array
		foreach ($criteria->getMap() as $criterion)
		{
			if (in_array(Criterion::ODER, $criterion->getConjunctions()))
			{
				continue;
			}
			
			if ($criterion->getComparison() == Criteria::IN && !$criterion->getValue())
			{
				KalturaLog::debug("kQueryCache: criteria has empty IN, returning empty result set, peer=$peerClassName");
				return array();
			}
		}
		
		// initialize
		$invalidationKeyRules = call_user_func(array($peerClassName, 'getCacheInvalidationKeys'));
		$invalidationKeys = self::getInvalidationKeysForQuery($invalidationKeyRules, $criteria);
		if (!$invalidationKeys)
		{
			return null;
		}
		
		self::initGlobalMemcache();
		if (self::$s_memcacheQueries === null)			// we must have both memcaches initialized
		{
			return null;
		}
		
		// build memcache query
		foreach ($invalidationKeys as $index => $invalidationKey)
		{
			$invalidationKeys[$index] = self::CACHE_PREFIX_INVALIDATION_KEY.$invalidationKey;
		}

		$keysToGet = $invalidationKeys;
		$keysToGet[] = self::DONT_CACHE_KEY;
		$keysToGet[] = self::MAX_SLAVE_LAG_KEY;
		
		$queryStart = microtime(true);
		$cacheResult = self::$s_memcacheKeys->multiGet($keysToGet);
		KalturaLog::debug("kQueryCache: keys query took " . (microtime(true) - $queryStart) . " seconds");
		
		if ($cacheResult === false)
		{
			KalturaLog::log("kQueryCache: failed to query keys memcache, not using query cache");
			return null;
		}

		// get max slave lag
		$queryMasterThreshold = self::MAX_QUERY_MASTER_TIME_MARGIN_SEC;
		$maxSlaveLag = null;		
		if (array_key_exists(self::MAX_SLAVE_LAG_KEY, $cacheResult) && 
			strlen($cacheResult[self::MAX_SLAVE_LAG_KEY]) && 
			is_numeric($cacheResult[self::MAX_SLAVE_LAG_KEY]))
		{
			$maxSlaveLag = $cacheResult[self::MAX_SLAVE_LAG_KEY];
			$maxSlaveLag += self::SLAVE_LAG_TIME_MARGIN_SEC;
			$queryMasterThreshold = min($maxSlaveLag, $queryMasterThreshold);
		}
		unset($cacheResult[self::MAX_SLAVE_LAG_KEY]);
		
		// don't cache the result if the 'dont cache' flag is enabled
		$cacheQuery = true;
		if (array_key_exists(self::DONT_CACHE_KEY, $cacheResult) && 
			$cacheResult[self::DONT_CACHE_KEY])
		{
			KalturaLog::log("kQueryCache: dontCache key is set -> not caching the result");
			$cacheQuery = false;
		}
		unset($cacheResult[self::DONT_CACHE_KEY]);
		
		// get max invalidation time
		$maxInvalidationTime = null;
		$maxInvalidationKey = null;
		if (count($cacheResult))
		{
			$maxInvalidationTime = max($cacheResult);
			$maxInvalidationKey = array_search($maxInvalidationTime, $cacheResult);
		}

		// check whether we should query the master
		$queryDB = self::QUERY_DB_SLAVE;
		$currentTime = time();		
		if (!is_null($maxInvalidationTime) && 
			$currentTime < $maxInvalidationTime + $queryMasterThreshold)
		{
			KalturaLog::debug("kQueryCache: changed recently -> query master, peer=$peerClassName, invkey=$maxInvalidationKey querytime=$currentTime invtime=$maxInvalidationTime threshold=$queryMasterThreshold");
			$queryDB = self::QUERY_DB_MASTER;
			if ($currentTime < $maxInvalidationTime + self::CLOCK_SYNC_TIME_MARGIN_SEC)
			{
				return null;			// The query won't be cached since cacheKey is null, it's ok cause it won't be used anyway
			}
		}

		if ($queryDB == self::QUERY_DB_SLAVE && 
			!is_null($maxInvalidationTime) && 
			$currentTime < $maxInvalidationTime + $maxSlaveLag)
		{
			KalturaLog::debug("kQueryCache: using an out of date slave -> not caching the result, peer=$peerClassName, invkey=$maxInvalidationKey querytime=$currentTime invtime=$maxInvalidationTime slavelag=$maxSlaveLag");
			$cacheQuery = false;
		}
					
		// get the cache key and update the api cache
		$origCacheKey = self::CACHE_PREFIX_QUERY . $queryType . md5(serialize($criteria) . self::CACHE_VERSION);
		if ($cacheQuery)
		{
			kApiCache::addInvalidationKeys($invalidationKeys, $maxInvalidationTime);
			$cacheKey = new kQueryCacheKey($origCacheKey); 
		}
		else 
		{
			kApiCache::disableConditionalCache();
		}
		
		// check whether we have a valid cached query
		$queryStart = microtime(true);
		$queryResult = self::$s_memcacheQueries->get($origCacheKey);
		KalturaLog::debug("kQueryCache: query took " . (microtime(true) - $queryStart) . " seconds");
		
		if (!$queryResult)
		{	
			KalturaLog::debug("kQueryCache: cache miss, peer=$peerClassName, key=$origCacheKey");
			return null;
		}
		
		list($queryResult, $queryTime, $debugInfo) = $queryResult;
		
		if (!is_null($maxInvalidationTime) && 
			$queryTime < $maxInvalidationTime + self::CLOCK_SYNC_TIME_MARGIN_SEC)
		{
			KalturaLog::debug("kQueryCache: cached query invalid, peer=$peerClassName, key=$origCacheKey, invkey=$maxInvalidationKey querytime=$queryTime debugInfo=$debugInfo invtime=$maxInvalidationTime");
			return null;
		}
		
		// return from memcache
		$existingInvKeys = array();
		foreach ($cacheResult as $invalidationKey => $invalidationTime)
		{
			$existingInvKeys[] = "$invalidationKey:$invalidationTime";
		}
		$existingInvKeys = implode(',', $existingInvKeys);
		
		KalturaLog::debug("kQueryCache: returning from memcache, peer=$peerClassName, key=$origCacheKey queryTime=$queryTime debugInfo=$debugInfo invkeys=[$existingInvKeys]");
		return $queryResult;
	}
	
	public static function cacheQueryResults($cacheKey, $queryResult)
	{
		if (self::$s_memcacheQueries === null || $cacheKey === null || 
			(is_array($queryResult) && count($queryResult) > self::MAX_CACHED_OBJECT_COUNT))
		{
			return;
		}

		$uniqueId = new UniqueId();
		$debugInfo = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : '');
		$debugInfo .= "[$uniqueId]";
		
		$queryTime = time();
		$key = $cacheKey->getKey();
		KalturaLog::debug("kQueryCache: Updating memcache, key=$key queryTime=$queryTime");
		self::$s_memcacheQueries->set($key, array($queryResult, $queryTime, $debugInfo), self::CACHED_QUERIES_EXPIRY_SEC);
	}
	
	public static function invalidateQueryCache($object)
	{
		if (!kConf::get("query_cache_invalidate_on_change"))
		{
			return;
		}
		
		$invalidationKeys = $object->getCacheInvalidationKeys();
		if (!$invalidationKeys)
		{
			return;
		}
		
		self::initGlobalMemcache();
		if (self::$s_memcacheKeys === null)			// The keys memcache suffices here
		{
			return null;
		}
				
		$currentTime = time();
		foreach ($invalidationKeys as $invalidationKey)
		{
			$invalidationKey = self::CACHE_PREFIX_INVALIDATION_KEY . str_replace(' ', '_', $invalidationKey);
			KalturaLog::debug("kQueryCache: updating invalidation key, invkey=$invalidationKey");
			if (!self::$s_memcacheKeys->set($invalidationKey, $currentTime, 
				self::CACHED_QUERIES_EXPIRY_SEC + self::INVALIDATION_KEYS_EXPIRY_MARGIN))
			{
				KalturaLog::err("kQueryCache: failed to update invalidation key");
			}
		}
	}
	
	public static function isCurrentQueryHandled()
	{
		return kQueryCacheKey::exists();
	}
}