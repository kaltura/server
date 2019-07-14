<?php


class ResourceReservation
{
	CONST DEFAULT_TIME_IN_CACHE_FOR_RESERVATION = 5;

	/**
	 * @var kBaseCacheWrapper $cache
	 */
	private $cache;

	/**
	 * @var int $ttl
	 */
	private $ttl;

	/**
	 * @var string $userToken
	 */
	private $userToken;


	function __construct($cache, $ttl, $userToken)
	{
		$this->cache = $cache;
		$this->userToken = $userToken;
		$this->ttl = intval($ttl);
		if (!$this->ttl)
			$this->ttl = ResourceReservation::DEFAULT_TIME_IN_CACHE_FOR_RESERVATION;
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
	 * will reserve the resource for some time
	 * @param string $resourceId
	 * @param bool $allowTimeExtension
	 * @return bool - true if reserve and false if could not
	 */
	public function reserve($resourceId, $allowTimeExtension = true)
	{
		if ($this->cache)
		{
			$key = $this->getCacheKeyForResource($resourceId);
			if ($this->cache->add($key, $this->userToken, $this->ttl))
			{
				return true;
			}
			$val = $this->cache->get($key);
			if ($allowTimeExtension && $val == $this->userToken) //only the one that reserve the resource can override the reserve (time extending)
			{
				return $this->cache->set($key, $this->userToken, $this->ttl);
			}
		}
		return false;
	}

	/**
	 * will reserve the resource for some time ignoring if the resource was already reserved
	 * @param string $resourceId
	 *
	 * @return bool - true if reserve and false if could not
	 */
	public function forceReserve($resourceId)
	{
		if ($this->cache)
			return $this->cache->set($this->getCacheKeyForResource($resourceId), $this->userToken, $this->ttl);
		return false;
	}

	/**
	 * will delete the reservation of the resource from cache
	 * @param string $resourceId
	 *
	 * @return bool - true if reservation was deleted
	 */
	public function deleteReservation($resourceId)
	{
		if ($this->cache) 
		{
			$val = $this->cache->get($this->getCacheKeyForResource($resourceId));
			if (!$val)
				return true;
			if ($val == $this->userToken) //only the one that reserve the resource can delete reservation
				return $this->cache->delete($this->getCacheKeyForResource($resourceId));
		}
		return false;
	}

	/**
	 * will return BatchJob objects.
	 * @param string $resourceId
	 *
	 * @return bool - true mean the resource is available
	 */
	public function checkAvailable($resourceId)
	{
		if ($this->cache)
		{
			$val = $this->cache->get($this->getCacheKeyForResource($resourceId));
			if (!$val || $val == $this->userToken) //if not in cache or the caller was the one that reserve it
				return true;
		}
		return false;
	}
}
