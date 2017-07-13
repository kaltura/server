<?php
/**
 * Created by IntelliJ IDEA.
 * User: moshe.maor
 * Date: 7/16/2017
 * Time: 5:04 PM
 */
/**
 * Sending beacons on objects
 *
 * @service beacon
 * @package plugins.beacon
 * @subpackage api.services
 */
class BeaconService extends KalturaBaseService{
    public function initService($serviceId, $serviceName, $actionName)
    {
        parent::initService($serviceId, $serviceName, $actionName);

    }

    /**
     * @action add
     * @param KalturaBeacon $beacon
     * @param bool $shouldLog
     * @param int $ttl
     * @return bool
     */
    public function addAction(KalturaBeacon $beacon , $shouldLog,$ttl=600)
    {
        //TODO add item to elastic server
        $beaconObject = new BeaconObject(kCurrentContext::$partner_id,$beacon->relatedObjectType, $beacon->eventType , $beacon->objectId ,$beacon->privateData);
        $beaconObject->indexObjectState();
        if($shouldLog)
        {
            $beaconObject->Log($ttl);
        }
        return true;
    }

    /**
     * @action getLast
     * @param KalturaBeacon $beaconParams
     * @param KalturaFilterPager $pager
     * @return KalturaBeaconListResponse
     */
    public function getLastAction($beaconParams, $pager )
    {
        if (!kCurrentContext::$is_admin_session)
        {
            throw new KalturaAPIException("Allowed only with admin KS");
        }
    }

    /**
     * @action enhanceSearch
     * @param KalturaBeacon $beaconParams
     * @param string $externalElasticQueryObject
     * @param KalturaFilterPager $pager
     * @return KalturaBeaconListResponse
     */

    public function enhanceSearchAction($beaconParams, $externalElasticQueryObject,$pager)
    {
        if (!kCurrentContext::$is_admin_session)
        {
            throw new KalturaAPIException("Allowed only with admin KS");
        }

        //TODO add item to elastic server

        //TODO add item to elastic server
    }


}