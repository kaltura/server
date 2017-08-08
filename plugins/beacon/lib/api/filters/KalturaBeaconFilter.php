<?php
/**
 * @package plugins.beacon
 * @subpackage api.filters
 */
class KalturaBeaconFilter extends KalturaBeaconBaseFilter
{
	/**
	 * @var string
	 */
	public $privateData;
	
    public function getCoreFilter()
    {
		return null;
    }

    public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
    {
		
    }

    public function searchLastBeacons(KalturaFilterPager $pager)
    {
		$queryParams = $this->createSearchObject();
		return $this->search($queryParams, kBeacon::FIELD_TYPE_VALUE_STATS, $pager);
    }

    public function enhanceSearch(KalturaFilterPager $pager)
    {
		$queryParams = $this->createSearchObject();
		return $this->search($queryParams, kBeacon::FIELD_TYPE_VALUE_LOG, $pager);
    }
    
    private function search($queryParams, $indexType, KalturaFilterPager $pager)
	{
		$elasticClient = new BeaconElasticClient();
		$responseArray = $elasticClient->search(kBeacon::FIELD_INDEX_VALUE, $indexType, $queryParams, $pager->pageSize, $pager->calcOffset());
		
		$response = new KalturaBeaconListResponse();
		$response->objects = KalturaBeaconArray::fromDbArray($responseArray);
        return $response;
	}

    protected function createSearchObject()
    {
		$searchObject = array();
		$searchObject[kBeacon::FIELD_RELATED_OBJECT_TYPE] = $this->relatedObjectTypeEqual;
		$searchObject[kBeacon::FIELD_OBJECT_ID] = $this->objectIdEqual;
		$searchObject[kBeacon::FIELD_EVENT_TYPE] = $this->eventTypeEqual;
		$searchObject[kBeacon::FIELD_PARTNER_ID] = kCurrentContext::getCurrentPartnerId();
		
		$privateDataSearchObject = $this->privateData ? json_decode($this->privateData) : null;
		if(!$privateDataSearchObject) 
			return $searchObject;
		
		foreach($privateDataSearchObject as $key => $value)
		{
		    $searchObject[$key] = $value;
		}
		return $searchObject;
    }

}