<?php

/**
 * Sending beacons on objects
 *
 * @service beacon
 * @package plugins.beacon
 * @subpackage api.services
 */
class BeaconService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		if (($actionName == 'getLast' || $actionName == 'enhanceSearch') && !kCurrentContext::$is_admin_session)
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName . '->' . $this->actionName);
		
		parent::initService($serviceId, $serviceName, $actionName);
	}
	
	/**
	 * @action add
	 * @param KalturaBeacon $beacon
	 * @param KalturaNullableBoolean $shouldLog
	 * @param int $ttl
	 * @return bool
	 */
	public function addAction(KalturaBeacon $beacon, $shouldLog = KalturaNullableBoolean::FALSE_VALUE, $ttl = dateUtils::DAY)
	{
		$beaconObj = $beacon->toInsertableObject();
		$beaconObj->index($shouldLog, $ttl);
		
		return true;
	}
	
	/**
	 * @action list
	 * @param KalturaBeaconFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaBeaconListResponse
	 * @throws KalturaAPIException
	 */
	public function listAction(KalturaBeaconFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaBeaconFilter();
		
		if (!$pager)
			$pager = new KalturaFilterPager();
		
		return $filter->getListResponse($pager);
	}
	
	/**
	 * @action enhanceSearch
	 * @param KalturaBeaconEnhanceFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaBeaconListResponse
	 * @throws KalturaAPIException
	 */
	
	public function enhanceSearchAction(KalturaBeaconEnhanceFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaBeaconFilter();
		
		if (!$pager)
			$pager = new KalturaFilterPager();
		
		return $filter->enhanceSearch($pager);
	}
}