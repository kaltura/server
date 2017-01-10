<?php

class kResourceReservation
{
	/**
	 * @var kBaseCacheWrapper $cache
	 */
	private $cache;

	/**
	 * @var int $ttl
	 */
	private $ttl;

	/**
	 * @var string $ks
	 */
	private $ks;

	function __construct($ttl = null)
	{
		$this->cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_RESOURCE_RESERVATION);
		$this->ks = kCurrentContext::$ks;
		if (!$ttl)
			$ttl = kConf::get('ResourceReservationTime');
		$this->ttl = $ttl;
	}


	/**
	 * will reserve the resource for some time
	 * @param string $resourceId
	 *
	 * @return bool - true if reserve and false if could not
	 */
	public function reserve($resourceId)
	{
		if (ResourceReservation::reserve($this->cache, $resourceId, $this->ks, $this->ttl))
		{
			KalturaLog::info("Resource reservation was done successfully for resource id [$resourceId] for [$this->ttl] seconds");
			return true;
		}
		KalturaLog::info("Could not reserve resource id [$resourceId]");
		return false;
	}

	/**
	 * will delete the reservation of the resource from cache
	 * @param string $resourceId
	 *
	 * @return bool - true if reserve and false if could not
	 */
	public function deleteReservation($resourceId)
	{
		if (ResourceReservation::deleteReservation($this->cache, $resourceId, $this->ks))
		{
			KalturaLog::info("Resource reservation was deleted successfully for resource id [$resourceId]");
			return true;
		}
		KalturaLog::info("Could not delete reservation for resource id [$resourceId]");
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
		if (ResourceReservation::checkAvailable($this->cache, $resourceId, $this->ks))
		{
			KalturaLog::info("Resource id [$resourceId] is available for usage");
			return true;
		}
		KalturaLog::info("Can not use resource id [$resourceId] - it is reserved");
		return false;
	}
}