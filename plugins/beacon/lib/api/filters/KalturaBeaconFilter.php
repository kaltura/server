<?php
/**
 * @package plugins.beacon
 * @subpackage api.filters
 */
class KalturaBeaconFilter extends KalturaRelatedFilter {

    /**
     * @var KalturaBeaconObjectTypes
     */
    public $relatedObjectType;

    /**
     * @var string
     */
    public $eventType;

    /**
     * @var string
     */
    public $objectId;

    /**
     * @var string
     */
    public $privateData;

    /**
     * @var string
     */
    public $created_at;

    public function getCoreFilter()
    {

    }

    public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
    {

    }

    public function searchLastBeacons(KalturaFilterPager $pager)
    {
        $response = new KalturaBeaconListResponse();
        $query = $this->createSearchObject();
        $partnerId = kCurrentContext::getCurrentPartnerId();
        $beaconObject  = new BeaconObject($partnerId , $query);
        $responseArray = $beaconObject->searchObject($pager->pageIndex ,$pager->pageSize );
        $response->objects = KalturaBeaconArray::fromDbArray($responseArray);
        return $response;
    }

    public function enhanceSearch($elasticQuery , KalturaFilterPager $pager)
    {
        $response = new KalturaBeaconListResponse();
        $query = $this->createSearchObject();
        $beaconObject  = new BeaconObject(CurrentContext::getCurrentPartnerId() , $query);
        $responseArray = $beaconObject->search($elasticQuery ,$pager->pageIndex ,$pager->pageSize );
        $response->objects = KalturaBeaconArray::fromDbArray($responseArray);
        return $response;
    }

    private function createSearchObject()
    {
        $searchObject = array();
        $searchObject[KalturaBeacon::RELATED_OBJECT_TYPE_STRING] = $this->relatedObjectType;
        $searchObject[KalturaBeacon::OBJECT_ID_STRING] = $this->objectId;
        $searchObject[KalturaBeacon::EVENT_TYPE_STRING] = $this->eventType;
        foreach($this->privateData as $key=>$value)
        {
            $searchObject[$key]=$value;
        }
        return $searchObject;
    }

}