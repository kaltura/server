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
	 * @param string $userSignature
	 * @param int $ttl
	 *
	 * @return bool - true if reserve and false if could not
	 */
	public static function reserve($cache, $resourceId, $userSignature, $ttl = ResourceReservation::DEFAULT_TIME_IN_CACHE_FOR_RESERVATION)
	{
		if (!$cache)
			return false;
		return $cache->add(self::getCacheKeyForResource($resourceId), $userSignature, $ttl);
	}

	/**
	 * will reserve the resource for some time ignoring if the resource was already reserved
	 * @param kBaseCacheWrapper $cache
	 * @param string $resourceId
	 * @param string $userSignature
	 * @param int $ttl
	 *
	 * @return bool - true if reserve and false if could not
	 */
	public static function reserveByForce($cache, $resourceId, $userSignature, $ttl = ResourceReservation::DEFAULT_TIME_IN_CACHE_FOR_RESERVATION)
	{
		if (!$cache)
			return false;
		return $cache->set(self::getCacheKeyForResource($resourceId), $userSignature, $ttl);
	}

	/**
	 * will return BatchJob objects.
	 * @param kBaseCacheWrapper $cache
	 * @param string $resourceId
	 * @param string $userSignature
	 *
	 * @return bool - true mean the resource is available
	 */
	public static function checkAvailable($cache, $resourceId, $userSignature)
	{
		if (!$cache)
			return false;
		$val = $cache->get(self::getCacheKeyForResource($resourceId));
		if (!$val || $val == $userSignature) //if not in cache or the caller was the one that reserve it
			return true;
		return false;
	}
}