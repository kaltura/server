<?php

class kQueryCache 
{
	const INVALIDATION_TIME_MARGIN_SEC = 300;		// When comparing the invalidation key timestamp to the query timestamp, 
													// the query timestamp should be greater by this value to use the cache
	const QUERY_MASTER_TIME_MARGIN_SEC = 300;		// The time frame after a change to a row during which we should query the master
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
		
		if (!class_exists('Memcache'))
		{
			return;
		}
		
		self::$s_memcache = new Memcache;

		//self::$s_memcache->setOption(Memcached::OPT_BINARY_PROTOCOL, true);			// TODO: enable when moving to memcached v1.3
		
		$connStart = microtime(true);
		$res = @self::$s_memcache->connect(kConf::get("global_memcache_host"), kConf::get("global_memcache_port"));
		KalturaLog::debug("kQueryCache: connect took - ". (microtime(true) - $connStart). " seconds to ".kConf::get("global_memcache_host"));
		if (!$res)
		{
			KalturaLog::err("kQueryCache: failed to connect to global memcache");
			self::$s_memcache = null;
		}
	}

	public static function getCachedQueryResults(Criteria $criteria, $queryType, $peerClassName, &$cacheKey)
	{
		// initialize
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
		
		// build memcache query
		foreach ($invalidationKeys as $index => $invalidationKey)
		{
			$invalidationKeys[$index] = self::CACHE_PREFIX_INVALIDATION_KEY.$invalidationKey;
		}
		$cacheKey = self::CACHE_PREFIX_QUERY.$queryType.md5(serialize($criteria));
		
		$keysToGet = $invalidationKeys;
		$keysToGet[] = $cacheKey;
		
		$queryStart = microtime(true);
		$cacheResult = self::$s_memcache->get($keysToGet);
		KalturaLog::debug("kQueryCache: query took " . (microtime(true) - $queryStart) . " seconds");
		
		// get the cached query
		$queryResult = null;
		if (array_key_exists($cacheKey, $cacheResult))
		{
			$queryResult = $cacheResult[$cacheKey];
			unset($cacheResult[$cacheKey]);
		}
		
		// check whether we should query the master
		$currentTime = time();
		foreach ($cacheResult as $invalidationKey => $invalidationTime)
		{
			if ($currentTime < $invalidationTime + self::QUERY_MASTER_TIME_MARGIN_SEC)
			{
				KalturaLog::debug("kQueryCache: changed recently -> query master, peer=$peerClassName, invkey=$invalidationKey querytime=$currentTime invtime=$invalidationTime");
				return null;
			}
		}
		
		// check whether we have a valid cached query
		if (!$queryResult)
		{	
			KalturaLog::debug("kQueryCache: cache miss, peer=$peerClassName, key=$cacheKey");
			return null;
		}
		
		list($queryResult, $queryTime) = $queryResult;
		
		$existingInvKeys = array();
		foreach ($cacheResult as $invalidationKey => $invalidationTime)
		{
			$existingInvKeys[] = "$invalidationKey:$invalidationTime";
			
			if ($queryTime < $invalidationTime + self::INVALIDATION_TIME_MARGIN_SEC)
			{
				KalturaLog::debug("kQueryCache: cached query invalid, peer=$peerClassName, key=$cacheKey, invkey=$invalidationKey querytime=$queryTime invtime=$invalidationTime");
				return null;
			}
		}
		
		// return from memcache
		$existingInvKeys = implode(',', $existingInvKeys);
		
		KalturaLog::debug("kQueryCache: returning from memcache, peer=$peerClassName, key=$cacheKey queryTime=$queryTime invkeys=[$existingInvKeys]");
		return $queryResult;
	}
	
	public static function cacheQueryResults($cacheKey, $queryResult)
	{
		if (self::$s_memcache === null || $cacheKey === null || 
			(is_array($queryResult) && count($queryResult) > self::MAX_CACHED_OBJECT_COUNT))
		{
			return;
		}
		
		$queryTime = time();
		KalturaLog::debug("kQueryCache: Updating memcache, key=$cacheKey queryTime=$queryTime");
		self::$s_memcache->set($cacheKey, array($queryResult, $queryTime), MEMCACHE_COMPRESSED, self::CACHED_QUERIES_EXPIRY_SEC);
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
