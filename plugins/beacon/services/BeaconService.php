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
    	if(($actionName == 'getLast' || $actionName == 'enhanceSearch') && !kCurrentContext::$is_admin_session)
    		throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
    	
        parent::initService($serviceId, $serviceName, $actionName);
    }

    /**
     * @action add
     * @param KalturaBeacon $beacon
     * @param KalturaNullableBoolean $shouldLog
     * @param int $ttl
     * @return bool
     */
    public function addAction(KalturaBeacon $beacon, $shouldLog = KalturaNullableBoolean::FALSE_VALUE, $ttl = 600)
    {
        $beacon->index($shouldLog, $ttl);
        return true;
    }

    /**
     * @action getLast
     * @param KalturaBeaconFilter $filter
     * @param KalturaFilterPager $pager
     * @return KalturaBeaconListResponse
     * @throws KalturaAPIException
     */
    public function getLastAction(KalturaBeaconFilter $filter = null, KalturaFilterPager $pager = null)
    {        
        if(!$filter)
        	$filter = new KalturaBeaconFilter();
        
        if(!$pager)
        	$pager = new KalturaFilterPager();

       	return $filter->searchLastBeacons($pager);
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
    	if(!$filter)
    		$filter = new KalturaBeaconFilter();
    	
    	if(!$pager)
    		$pager = new KalturaFilterPager();
    	
        return $beaconFilter->enhanceSearch($pager);
    }


}