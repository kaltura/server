<?php

/* class is an extension of ResourceReservation where the value saved to cache is not necessarily the userToken
 *
 */
class varResourceReservation extends ResourceReservation
{

	CONST DEFAULT_TIME_IN_CACHE_FOR_RESERVATION = 600;

	function __construct($cache, $ttl, $userToken)
	{
		if (!$ttl)
		{
			$ttl = varResourceReservation::DEFAULT_TIME_IN_CACHE_FOR_RESERVATION;
		}
		parent::__construct($cache, $ttl, $userToken);
	}

	/**
	 * will return BatchJob objects.
	 * @param string $resourceId
	 *
	 * @return bool - true mean the resource is available
	 */
	public function checkAvailable($resourceId)
	{
		$val = $this->retrieveValue($resourceId);
		if (!$val)
		{
			return true;
		}
		return false;
	}

	/**
	 * will retrieve the value for specific resourceId
	 * @param string $resourceId
	 *
	 * @return string - if the key reserved we will retrieve its value
	 */
	public function retrieveValue($resourceId)
	{
		if ($this->cache)
		{
			return $this->cache->get($this->getCacheKeyForResource($resourceId));
		}
		return null;
	}

	/**
	 * will reserve specific resourceId with specific value (which is not necessarily the userToken)
	 * @param string $resourceId
	 * @param string $value
	 *
	 * @return bool - if the key reserved we will true, else false
	 */
	public function reserveWithValue($resourceId, $value)
	{
		if ($this->cache)
		{
			$key = $this->getCacheKeyForResource($resourceId);
			if ($this->cache->add($key, $value, $this->ttl))
				return true;
			return $this->cache->set($key, $value, $this->ttl);
		}
		return false;
	}

}