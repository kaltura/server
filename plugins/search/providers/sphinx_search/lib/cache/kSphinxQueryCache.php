<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage cache
 */

class kSphinxQueryCache extends kQueryCache
{
	const MIN_CONDITIONAL_EXPIRY_INVALIDATION_WAIT = 30;	// min expiry time of conditional sphinx queries due to expected
															// sphinx server update which will provide real sphinx conditional cache
	
	const CACHE_PREFIX_QUERY = 'QCQSPH-';				// = Query Cache - Sphinx Query
	const CACHE_PREFIX_INVALIDATION_KEY = 'QCISPH-';	// = Query Cache - Invalidation key
	const DONT_CACHE_KEY = 'QCCSPH-DontCache';			// when set new queries won't be cached in the memcache
	const SPHINX_LAG_KEY = 'QCCSPH-SphinxLag';	// the lags of the diffrent sphinx servers in the current DC
	
	protected static $sphinxLag = null;
	protected static $maxInvalidationTime = null;
	protected static $maxInvalidationKey = null;
	protected static $reduceConditionalExpiry = 0;
	
	public static function getCachedSphinxQueryResults(Criteria $criteria, $objectClass, &$cacheKey)
	{
		self::$reduceConditionalExpiry = 0;
		
		if (!kConf::get("sphinx_query_cache_enabled"))
		{
			return null;
		}

		$invalidationKeyRules = call_user_func(array($objectClass, 'getCacheInvalidationKeys'));
		if (!$invalidationKeyRules)
			return null;

		$invalidationKeys = self::getInvalidationKeysForQuery($invalidationKeyRules, $criteria);
		if (!$invalidationKeys)
			return null;

		//KalturaLog::log("sphinx invalidationKeys ".print_r($invalidationKeys, true));

		self::initGlobalMemcache();
		if (self::$s_memcacheQueries === null)                  // we must have both memcaches initialized
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
		$keysToGet[] = self::SPHINX_LAG_KEY;

		$queryStart = microtime(true);
		$cacheResult = self::$s_memcacheKeys->multiGet($keysToGet);
		KalturaLog::debug("kQueryCache: keys query took " . (microtime(true) - $queryStart) . " seconds");

		if ($cacheResult === false)
		{
			KalturaLog::log("kQueryCache: failed to query keys memcache, not using query cache");
			return null;
		}

		// don't cache the result if the 'dont cache' flag is enabled
		$cacheQuery = true;
		if (array_key_exists(self::DONT_CACHE_KEY, $cacheResult) && 
			$cacheResult[self::DONT_CACHE_KEY])
		{
			KalturaLog::log("kQueryCache: dontCache key is set -> not caching the result");
			$cacheQuery = false;
		}
		unset($cacheResult[self::DONT_CACHE_KEY]);

		if (array_key_exists(self::SPHINX_LAG_KEY, $cacheResult) && 
			strlen($cacheResult[self::SPHINX_LAG_KEY]))
		{
			self::$sphinxLag = json_decode($cacheResult[self::SPHINX_LAG_KEY], true);
		}
		unset($cacheResult[self::SPHINX_LAG_KEY]);
		
		// get max invalidation time
		$maxInvalidationTime = null;
		$maxInvalidationKey = null;
		if (count($cacheResult))
		{
			$maxInvalidationTime = max($cacheResult);
			$maxInvalidationKey = array_search($maxInvalidationTime, $cacheResult);
		}

		$currentTime = time();		
		if (!is_null($maxInvalidationTime) && 
			$currentTime < $maxInvalidationTime + self::CLOCK_SYNC_TIME_MARGIN_SEC)
		{
			self::$reduceConditionalExpiry = self::CLOCK_SYNC_TIME_MARGIN_SEC;
			return null;			// The query won't be cached since cacheKey is null, it's ok cause it won't be used anyway
		}
			
		self::$maxInvalidationTime = $maxInvalidationTime;
		self::$maxInvalidationKey = $maxInvalidationKey;
		
		// get the cache key and update the api cache
		$cacheKey = self::CACHE_PREFIX_QUERY . md5(serialize($criteria) . self::CACHE_VERSION);
		if ($cacheKey)
		{
			kApiCache::addInvalidationKeys($invalidationKeys, $maxInvalidationTime);
		}

		// check whether we have a valid cached query
		$queryStart = microtime(true);
		$queryResult = self::$s_memcacheQueries->get($cacheKey);
		KalturaLog::debug("kQueryCache: query took " . (microtime(true) - $queryStart) . " seconds");

		if (!$queryResult)
		{
			KalturaLog::debug("kQueryCache: cache miss, peer=$objectClass, key=$cacheKey");
			return null;
		}

		list($queryResult, $queryTime, $debugInfo) = $queryResult;

		if (!is_null($maxInvalidationTime) &&
			$queryTime < $maxInvalidationTime + self::CLOCK_SYNC_TIME_MARGIN_SEC)
		{
			KalturaLog::debug("kQueryCache: cached query invalid, peer=$objectClass, key=$cacheKey, invkey=$maxInvalidationKey querytime=$queryTime debugInfo=$debugInfo invtime=$maxInvalidationTime");
			return null;
		}
		
		// return from memcache
		$existingInvKeys = array();
		foreach ($cacheResult as $invalidationKey => $invalidationTime)
		{
			$existingInvKeys[] = "$invalidationKey:$invalidationTime";
		}
		$existingInvKeys = implode(',', $existingInvKeys);

		KalturaLog::debug("kQueryCache: returning from memcache, peer=$objectClass, key=$cacheKey queryTime=$queryTime debugInfo=$debugInfo invkeys=[$existingInvKeys]");
		return $queryResult;
	}

	public static function cacheSphinxQueryResults($pdo, $objectClass, $cacheKey, $queryResult, $sqlConditions)
	{
		$cached = self::internalCacheSphinxQueryResults($pdo, $objectClass, $cacheKey, $queryResult);
		
		if (!$cached && count($sqlConditions))
		{
			foreach($sqlConditions as $sqlCondition)
				call_user_func_array("kApiCache::addSqlQueryCondition", $sqlCondition);

			// if invalidation keys exists but the query couldn't be cached yet, shortne the expiry
			// in hope to have full api caching in one of the next calls
			if (self::$reduceConditionalExpiry)
			{
				$finalExpiry = max(self::$reduceConditionalExpiry, self::MIN_CONDITIONAL_EXPIRY_INVALIDATION_WAIT);
				KalturaLog::debug("kQueryCache: setConditionalCacheExpiry targetExpiry=".self::$reduceConditionalExpiry." finalExpiry=$finalExpiry");
				kApiCache::setConditionalCacheExpiry($finalExpiry);
			}
		}
	}
		
	protected static function internalCacheSphinxQueryResults($pdo, $objectClass, $cacheKey, $queryResult)
	{
		if (self::$s_memcacheQueries === null || $cacheKey === null)
		{
			return false;
		}

		$hostName = $pdo->getHostName();
		if (!is_array(self::$sphinxLag) || !array_key_exists($hostName, self::$sphinxLag))
			return false; // don't cache if sphinx lag isn't known

		$queryTime = self::$sphinxLag[$hostName];

		if (self::$maxInvalidationTime > $queryTime )
		{
			// in case of sphinx conditional queries, shorten the cache expiry till the sphinx server will reach the required invalidation update time
			self::$reduceConditionalExpiry = self::$maxInvalidationTime - $queryTime;
			$currentTime = time();
			KalturaLog::debug("kQueryCache: using an out of date sphinx  -> not caching the result, peer=$objectClass, invkey=".self::$maxInvalidationKey." querytime=$currentTime invtime=".self::$maxInvalidationTime." sphinxLag=$queryTime");
			return false;
		}
		
		$uniqueId = new UniqueId();
		$debugInfo = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : '');
		$debugInfo .= "[$uniqueId]";

		KalturaLog::debug("kQueryCache: Updating memcache, key=$cacheKey queryTime=$queryTime");
		self::$s_memcacheQueries->set($cacheKey, array($queryResult, $queryTime, $debugInfo), self::CACHED_QUERIES_EXPIRY_SEC);
		
		return true;
	}

	public static function invalidateQueryCache($object)
	{
		if (!kConf::get("sphinx_query_cache_invalidate_on_change"))
		{
			return;
		}

		$objectClass = $object->getIndexObjectName();
		$invalidationKeys = $objectClass::getCacheInvalidationKeys($object);
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

}
