<?php

class kResourceReservation
{
	/**
	 * @var BinaryResourceReservation $resourceReservator
	 */
	private $resourceReservator;

	function __construct($ttl = null, $binaryReservator = false)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_RESOURCE_RESERVATION);
		$ks = kCurrentContext::$ks;
		if (!$ttl)
		{
			$ttl = kConf::get('ResourceReservationDuration');
		}

		if($binaryReservator)
		{
			$this->resourceReservator = new BinaryResourceReservation($cache, $ttl, $ks);
		}
		else
		{
			$this->resourceReservator = new ResourceReservation($cache, $ttl, $ks);
		}
	}


	/**
	 * will reserve the resource for some time
	 * @param string $resourceId
	 * @return bool - true if reserve and false if could not
	 */
	public function reserve($resourceId)
	{
		if ($this->resourceReservator->reserve($resourceId))
		{
			KalturaLog::info("Resource reservation was done successfully for resource id [$resourceId]");
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
		if ($this->resourceReservator->deleteReservation($resourceId))
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
		if ($this->resourceReservator->checkAvailable($resourceId))
		{
			KalturaLog::info("Resource id [$resourceId] is available for usage");
			return true;
		}
		KalturaLog::info("Can not use resource id [$resourceId] - it is reserved");
		return false;
	}
}