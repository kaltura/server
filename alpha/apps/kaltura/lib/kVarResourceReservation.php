<?php

/* class is an extension of ResourceReservation where the value saved to cache is not necessarily the userToken
 *
 */
class kVarResourceReservation extends kResourceReservation
{

	function __construct($ttl = null)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_RESOURCE_RESERVATION);
		$ks = kCurrentContext::$ks;
		if (!$ttl)
		{
			$ttl = kConf::get('ResourceReservationDuration');
		}
		$this->resourceReservator = new varResourceReservation($cache, $ttl, $ks);
	}

	/**
	 * will retrieve the value for specific resourceId
	 * @param string $resourceId
	 *
	 * @return string - if the key reserved we will retrieve its value
	 */
	public function retrieveValue($resourceId)
	{
		$value = $this->resourceReservator->retrieveValue($resourceId);
		if ($value)
		{
			KalturaLog::info("Resource id [$resourceId] is already stored. Value: [$value]");
		}
		else
		{
			KalturaLog::info("Resource id [$resourceId] is not reserved");
		}
		return $value;
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
		if ($this->resourceReservator->reserveWithValue($resourceId, $value))
		{
			KalturaLog::info("Resource reservation was done successfully for resource id: [$resourceId] value: [$value]");
			return true;
		}
		KalturaLog::info("Could not reserve resource id [$resourceId] value: [$value]");
		return false;
	}

}