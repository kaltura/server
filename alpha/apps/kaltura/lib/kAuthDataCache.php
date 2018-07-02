<?php

class kAuthDataCache
{
	const DEFAULT_TIME_IN_CACHE_FOR_AUTH_CACHED_DATA = 1800; //half an hour

	/**
	 * @var kBaseCacheWrapper $cache
	 */
	private $cache;

	/**
	* @var int $ttl
	*/
	private $ttl;

	function __construct()
	{
		$this->cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_AUTH_CACHED_DATA);
		$this->ttl = kConf::get('AuthCachedDataDuration','local', self::DEFAULT_TIME_IN_CACHE_FOR_AUTH_CACHED_DATA);
	}

	/**
	 * will return cache-key for the auth data
	 * @param string $dataId
	 * @return string
	 */
	private static function getCacheKeyForAuthData($dataId)
	{
		return "auth_data_cache_key_$dataId";
	}

	/**
	 * will store the resource for some time
	 * @param string $dataId
	 * @param array $authData
	 * @return bool - true if reserve and false if could not
	 */
	public function store($dataId, $authData)
	{
		if ($this->cache)
		{
			$key = self::getCacheKeyForAuthData($dataId);
			if ($this->cache->add($key, $authData, $this->ttl))
			{
				KalturaLog::info("Auth data was stored successfully for id [$dataId]");
				return true;
			}

			KalturaLog::ERR("Could not store auth data id [$dataId]");
		}

		return false;
	}

	/**
	 * will reserve the resource for some time
	 * @param string $dataId
	 * @return array - the auth data or false on error
	 */
	public function retrieve($dataId)
	{
		if ($this->cache)
		{
			$key = self::getCacheKeyForAuthData($dataId);
			return $this->cache->get($key);
		}

		return false;
	}
}