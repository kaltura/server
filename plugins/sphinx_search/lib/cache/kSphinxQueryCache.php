<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage cache
 */

class kSphinxQueryCache extends kQueryCache
{
	const CACHE_PREFIX_QUERY_SPHINX = 'QCQSPH-';				// = Query Cache - Sphinx Query
	const CACHE_PREFIX_INVALIDATION_KEY = 'QCISPH-';	// = Query Cache - Invalidation key

	public static function getCachedSphinxQueryResults(Criteria $criteria, $objectClass, &$cacheKey)
	{
		if (!kConf::get("query_cache_enabled"))
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
		//$keysToGet[] = self::DONT_CACHE_KEY;
		//$keysToGet[] = self::MAX_SLAVE_LAG_KEY;

		$queryStart = microtime(true);
		$cacheResult = self::$s_memcacheKeys->multiGet($keysToGet);
		KalturaLog::debug("kQueryCache: keys query took " . (microtime(true) - $queryStart) . " seconds");

		if ($cacheResult === false)
		{
			KalturaLog::log("kQueryCache: failed to query keys memcache, not using query cache");
			return null;
		}

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
			return null;			// The query won't be cached since cacheKey is null, it's ok cause it won't be used anyway

		// get the cache key and update the api cache
		$cacheKey = self::CACHE_PREFIX_QUERY_SPHINX . md5(serialize($criteria) . self::CACHE_VERSION);
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

	public static function cacheSphinxQueryResults($cacheKey, $queryResult)
	{
		if (self::$s_memcacheQueries === null || $cacheKey === null)
		{
			return;
		}

		$uniqueId = new UniqueId();
		$debugInfo = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : '');
		$debugInfo .= "[$uniqueId]";

		$queryTime = time();
		KalturaLog::debug("kQueryCache: Updating memcache, key=$cacheKey queryTime=$queryTime");
		self::$s_memcacheQueries->set($cacheKey, array($queryResult, $queryTime, $debugInfo), self::CACHED_QUERIES_EXPIRY_SEC);
	}

	public static function invalidateQueryCache($object)
	{
		if (!kConf::get("query_cache_invalidate_on_change"))
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
