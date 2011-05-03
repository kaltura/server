<?php

class kQueryCache 
{
	const INVALIDATION_TIME_MARGIN_SEC = 10;		// When comparing the invalidation key timestamp to the query timestamp, 
													// the query timestamp should be greater by this value to use the cache
	const MAX_CACHED_OBJECT_COUNT = 100;			// Select queries that return more objects than this const will not be cached
	const CACHED_QUERIES_EXPIRY_SEC = 86400;		// The expiry of the query keys in the memcache 	

	const CACHE_PREFIX_QUERY = 'QCQ-';				// = Query Cache - Query
	const CACHE_PREFIX_INVALIDATION_KEY = 'QCI-';	// = Query Cache - Invalidation key
	
	const QUERY_TYPE_SELECT = 'sel-';
	const QUERY_TYPE_COUNT =  'cnt-';
	
	protected static $s_memcache = null;
	protected static $s_memcacheInited = false;
	
	protected static function initGlobalMemcache()
	{
		if (self::$s_memcacheInited)
		{
			return;
		}
		
		self::$s_memcacheInited = true;
		
		if (!function_exists('memcache_connect'))
		{
			return;
		}
		
		self::$s_memcache = new Memcache;
		$res = @self::$s_memcache->connect(kConf::get("global_memcache_host"), kConf::get("global_memcache_port"));
		if (!$res)
		{
			KalturaLog::err("kQueryCache: failed to connect to global memcache");
			self::$s_memcache = null;
		}
	}

	public static function getCachedQueryResults(Criteria $criteria, $queryType, $peerClassName, &$cacheKey)
	{
		if (!kConf::get("query_cache_enabled"))
		{
			return null;
		}
		
		$invalidationKeys = call_user_func(array($peerClassName, 'getCacheInvalidationKeys'), $criteria, $queryType);
		if (!$invalidationKeys)
		{
			return null;
		}
		
		self::initGlobalMemcache();
		if (self::$s_memcache === null)
		{
			return null;
		}
		
		foreach ($invalidationKeys as &$invalidationKey)
		{
			$invalidationKey = self::CACHE_PREFIX_INVALIDATION_KEY.$invalidationKey;
		}
		$cacheKey = self::CACHE_PREFIX_QUERY.$queryType.md5(serialize($criteria));
		
		$keysToGet = $invalidationKeys;
		$keysToGet[] = $cacheKey;
		$cacheResult = self::$s_memcache->get($keysToGet);
		if (!array_key_exists($cacheKey, $cacheResult))
		{	
			KalturaLog::debug("kQueryCache: cache miss, key=$cacheKey");
			return null;
		}
		
		list($queryResult, $queryTime) = $cacheResult[$cacheKey];
		
		foreach ($invalidationKeys as $invalidationKey)
		{
			if (array_key_exists($invalidationKey, $cacheResult) &&
				$queryTime < $cacheResult[$invalidationKey] + self::INVALIDATION_TIME_MARGIN_SEC)
			{
				KalturaLog::debug("kQueryCache: cached query invalid, key=$cacheKey invkey=$invalidationKey querytime=$queryTime invtime={$cacheResult[$invalidationKey]}");
				return null;
			}
		}
		
		KalturaLog::debug("kQueryCache: returning from memcache, key=$cacheKey");
		return $queryResult;
	}
	
	public static function cacheQueryResults($cacheKey, $queryResult)
	{
		if (self::$s_memcache === null || $cacheKey === null || 
			(is_array($queryResult) && count($queryResult) > self::MAX_CACHED_OBJECT_COUNT))
		{
			return;
		}
		
		KalturaLog::debug("kQueryCache: Updating memcache, key=$cacheKey");
		self::$s_memcache->set($cacheKey, array($queryResult, time()), 0, self::CACHED_QUERIES_EXPIRY_SEC);
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
		if (self::$s_memcache === null)
		{
			return null;
		}
				
		$currentTime = time();
		foreach ($invalidationKeys as $invalidationKey)
		{
			$invalidationKey = self::CACHE_PREFIX_INVALIDATION_KEY.$invalidationKey;
			KalturaLog::debug("kQueryCache: updating invalidation key, invkey=$invalidationKey");
			self::$s_memcache->set($invalidationKey, $currentTime);
		}
	}
}
