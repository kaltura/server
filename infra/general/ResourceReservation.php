<?php


class ResourceReservation
{
	CONST DEFAULT_TIME_IN_CACHE_FOR_RESERVATION = 5;
	
	/**
	 * will return cache-key for resource
	 * @param string $resourceId
	 * @return string
	 */
	private static function getCacheKeyForResource($resourceId)
	{
		return "resource_reservation_cache_key_$resourceId";
	}

	/**
	 * will reserve the resource for some time
	 * @param kBaseCacheWrapper $cache
	 * @param string $resourceId
	 * @param string $userToken
	 * @param int $ttl
	 *
	 * @return bool - true if reserve and false if could not
	 */
	public static function reserve($cache, $resourceId, $userToken, $ttl)
	{
		if (!$ttl)
			$ttl = ResourceReservation::DEFAULT_TIME_IN_CACHE_FOR_RESERVATION;
		if ($cache)
			return $cache->add(self::getCacheKeyForResource($resourceId), $userToken, $ttl);
		return false;
	}

	/**
	 * will reserve the resource for some time ignoring if the resource was already reserved
	 * @param kBaseCacheWrapper $cache
	 * @param string $resourceId
	 * @param string $userToken
	 * @param int $ttl
	 *
	 * @return bool - true if reserve and false if could not
	 */
	public static function reserveByForce($cache, $resourceId, $userToken, $ttl)
	{
		if (!$ttl)
			$ttl = ResourceReservation::DEFAULT_TIME_IN_CACHE_FOR_RESERVATION;
		if ($cache)
			return $cache->set(self::getCacheKeyForResource($resourceId), $userToken, $ttl);
		return false;
	}

	/**
	 * will delete the reservation of the resource from cache
	 * @param kBaseCacheWrapper $cache
	 * @param string $resourceId
	 * @param string $userToken
	 *
	 * @return bool - true if reservation was deleted
	 */
	public static function deleteReservation($cache, $resourceId, $userToken)
	{
		if ($cache) 
		{
			$val = $cache->get(self::getCacheKeyForResource($resourceId));
			if (!$val)
				return true;
			if ($val == $userToken)
				return $cache->delete(self::getCacheKeyForResource($resourceId));
		}
		return false;
	}

	/**
	 * will return BatchJob objects.
	 * @param kBaseCacheWrapper $cache
	 * @param string $resourceId
	 * @param string $userToken
	 *
	 * @return bool - true mean the resource is available
	 */
	public static function checkAvailable($cache, $resourceId, $userToken)
	{
		if ($cache)
		{
			$val = $cache->get(self::getCacheKeyForResource($resourceId));
			if (!$val || $val == $userToken) //if not in cache or the caller was the one that reserve it
				return true;
		}
		return false;
	}
}