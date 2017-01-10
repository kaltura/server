<?php

class kResourceReservation
{

	/**
	 * will reserve the resource for some time
	 * @param string $resourceId
	 * @param int $ttl
	 *
	 * @return bool - true if reserve and false if could not
	 */
	public static function reserve($resourceId, $ttl = null)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_RESOURCE_RESERVATION);
		if (!$cache)
			return false;
		if (!$ttl)
			$ttl = kConf::get('ResourceReservationTime');
		$ks = kCurrentContext::$ks;
		if (ResourceReservation::reserve($cache, $resourceId, $ks, $ttl))
		{
			KalturaLog::info("Resource reservation was done successfully for resource id [$resourceId] for [$ttl] seconds");
			return true;
		}
		KalturaLog::info("Could not reserve resource id [$resourceId]");
		return false;
	}

	/**
	 * will return BatchJob objects.
	 * @param string $resourceId
	 *
	 * @return bool - true mean the resource is available
	 */
	public static function checkAvailable($resourceId)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_RESOURCE_RESERVATION);
		if (!$cache)
			return false;
		$ks = kCurrentContext::$ks;
		if (ResourceReservation::checkAvailable($cache, $resourceId, $ks))
		{
			KalturaLog::info("Resource id [$resourceId] is available for usage");
			return true;
		}
		KalturaLog::info("Can not use resource id [$resourceId] - it is reserved");
		return false;
	}
}