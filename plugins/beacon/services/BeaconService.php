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
            throw new KalturaAPIException(MISSING_MANDATORY_PARAMETER,KalturaBeacon::EVENT_TYPE_STRING);

        if($beacon->objectId == null)
            throw new KalturaAPIException(MISSING_MANDATORY_PARAMETER,KalturaBeacon::OBJECT_ID_STRING);

        if($beacon->relatedObjectType == null)
            throw new KalturaAPIException(MISSING_MANDATORY_PARAMETER,KalturaBeacon::RELATED_OBJECT_TYPE_STRING);

        $beacon->indexObjectState();
        if($shouldLog)
        {
            $beacon->logObjectState($ttl);
        }
        return true;
    }

    /**
     * @action getLast
     * @param KalturaBeaconFilter $beaconFilter
     * @param KalturaFilterPager $pager
     * @return KalturaBeaconListResponse
     * @throws KalturaAPIException
     */
    public function getLastAction($beaconFilter, $pager )
    {
        if (!kCurrentContext::$is_admin_session)
        {
            throw new KalturaAPIException("Allowed only with admin KS");
        }

        $response = new KalturaBeaconListResponse();

        $response =  $beaconFilter->searchLastBeacons($pager);
        return $response;
    }

    /**
     * @action enhanceSearch
     * @param KalturaBeaconFilter $beaconFilter
     * @param string $externalElasticQueryObject
     * @param KalturaFilterPager $pager
     * @return KalturaBeaconListResponse
     * @throws KalturaAPIException
     */

    public function enhanceSearchAction($beaconFilter, $externalElasticQueryObject,$pager)
    {
        if (!kCurrentContext::$is_admin_session)
        {
            throw new KalturaAPIException("Allowed only with admin KS");
        }

        return $beaconFilter->enhanceSearch($externalElasticQueryObject,pager);

    }


}