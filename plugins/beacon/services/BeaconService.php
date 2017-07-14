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
        //validate input
        if($beacon->eventType == null)
            throw new KalturaAPIException(MISSING_MANDATORY_PARAMETER,'eventType');

        if($beacon->objectId == null)
            throw new KalturaAPIException(MISSING_MANDATORY_PARAMETER,'objectId');

        if($beacon->relatedObjectType == null)
            throw new KalturaAPIException(MISSING_MANDATORY_PARAMETER,'relatedObjectType');

        $beaconObject = new BeaconObject(kCurrentContext::getCurrentPartnerId(),$beacon);
        $beaconObject->indexObjectState();
        if($shouldLog)
        {
            $beaconObject->Log($ttl);
        }
        return true;
    }

    /**
     * @action getLast
     * @param KalturaBeaconFilter $beaconParams
     * @param KalturaFilterPager $pager
     * @return KalturaBeaconListResponse
     */
    public function getLastAction($beaconParams, $pager )
    {
        if (!kCurrentContext::$is_admin_session)
        {
            throw new KalturaAPIException("Allowed only with admin KS");
        }

        $response = new KalturaBeaconListResponse();

        $beaconObject = new BeaconObject(kCurrentContext::getCurrentPartnerId(),$beaconParams);
        $response->objects =  $beaconObject->searchObject($pager->pageSize,$pager->pageIndex);
        return $response;
    }

    /**
     * @action enhanceSearch
     * @param KalturaBeaconFilter $beaconParams
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
        $response = new KalturaBeaconListResponse();
        $beaconObject = new BeaconObject(kCurrentContext::$partner_id,$beaconParams);
        $response->objects =  $beaconObject->search($externalElasticQueryObject,$pager->pageSize,$pager->pageIndex);

        return $response;
    }


}