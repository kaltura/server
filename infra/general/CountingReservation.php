<?php
/**
 *
 */

class CountingReservation
{
	const DEFAULT_TIME_IN_CACHE_FOR_RESERVATION = 300;

	/**
	 * @var kBaseCacheWrapper $cache
	 */
	private $cache;

	/**
	 * @var int $ttl
	 */
	private $ttl;

	/**
	 * @var int $maxValue
	 */
	private $maxValue;

	function __construct($cache, $ttl, $maxValue)
	{
		$this->cache = $cache;
		$this->maxValue = $maxValue;
		$this->ttl = intval($ttl);
		if (!$this->ttl)
			$this->ttl = self::DEFAULT_TIME_IN_CACHE_FOR_RESERVATION;
	}

	/**
	 * will return cache-key for resource
	 * @param string $resourceId
	 * @return string
	 */
	private function getCacheKeyForResource($resourceId)
	{
		return "resource_reservation_cache_key_$resourceId";
	}

	/**
	 * will try to acquire the resource, if we tried more then max times we will lock the resource
	 * for the ttl set up for the reservator
	 * @param string $resourceId
	 * @return bool
	 */
	public function tryAcquire($resourceId)
	{
		$cacheCounter = $this->cache->get($this->getCacheKeyForResource($resourceId));
		if ($cacheCounter)
		{
			KalturaLog::info("Resource id [$resourceId] is already stored. Existing counter value: [$cacheCounter]");
			$cacheCounter = $cacheCounter - 1;
			if ($cacheCounter <= 0)
			{
				KalturaLog::info("Resource was acquired more then [$this->maxValue] times");
				return false;
			}
			else
			{
				$this->storeResourceInCache($resourceId);
			}
		}
		else
		{
			KalturaLog::info("Resource id [$resourceId] is not stored. Adding with counter value: [$this->maxValue]");
			$this->storeResourceInCache($resourceId);
		}
		return true;
	}

	/**
	 * will try to add or set the resource value (counter value) in cache
	 * @param string $resourceId
	 * @param int $cacheCounter
	 * @return bool
	 */
	private function storeResourceInCache($resourceId)
	{
		$key = $this->getCacheKeyForResource($resourceId);
		if ($this->cache->add($key, $this->maxValue, $this->ttl))
		{
			return true;
		}
		if ($this->cache->decrement($key, 1))
		{
			return true;
		}
		KalturaLog::info("Could not set counter value");
		return false;
	}

}