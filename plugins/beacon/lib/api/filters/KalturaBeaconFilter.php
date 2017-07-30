<?php
/**
 * @package plugins.beacon
 * @subpackage api.filters
 */
class KalturaBeaconFilter extends KalturaBeaconBaseFilter
{
    public function getCoreFilter()
    {
		return null;
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
        $responseArray = $beaconObject->searchObject($pager->pageSize,$pager->pageIndex  );
        $response->objects = KalturaBeaconArray::fromDbArray($responseArray);
        return $response;
    }

    public function enhanceSearch(KalturaFilterPager $pager)
    {
        $response = new KalturaBeaconListResponse();
        $query = $this->createSearchObject();
        $beaconObject  = new BeaconObject(kCurrentContext::getCurrentPartnerId() , $query);
        $responseArray = $beaconObject->search($pager->pageSize,$pager->pageIndex);
        $response->objects = KalturaBeaconArray::fromDbArray($responseArray);
        return $response;
    }

    protected function createSearchObject()
    {
        $searchObject = array();
        $searchObject[kBeacon::RELATED_OBJECT_TYPE_STRING] = $this->relatedObjectTypeEqual;
        $searchObject[kBeacon::OBJECT_ID_STRING] = $this->objectIdEqual;
        $searchObject[kBeacon::EVENT_TYPE_STRING] = $this->eventTypeEqual;
        //foreach($this->privateData as $key=>$value)
        //{
        //    $searchObject[$key]=$value;
        //}
        return $searchObject;
    }

}