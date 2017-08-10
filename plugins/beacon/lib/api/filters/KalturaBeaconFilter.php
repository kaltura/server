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
		$searchObject = $this->createSearchObject();
		$orderObject = $this->getOrderByObject();
		return $this->search($searchObject, $orderObject, kBeacon::FIELD_TYPE_VALUE_STATS, $pager);
    }

    public function enhanceSearch(KalturaFilterPager $pager)
    {
		$queryParams = $this->createSearchObject();
		return $this->search($queryParams, kBeacon::FIELD_TYPE_VALUE_LOG, $pager);
    }
    
    private function search($searchObject, $orderObject,  $indexType, KalturaFilterPager $pager)
	{
		$elasticClient = new BeaconElasticClient();
		$responseArray = $elasticClient->search(kBeacon::FIELD_INDEX_VALUE, $indexType, $searchObject, $orderObject, $pager->pageSize, $pager->calcOffset());
		
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
    
    public function getOrderByObject()
	{
		if(!$this->orderBy)
			return null;
		
		$orderObject = array();
		
		$order_arr = explode ( "," , $this->orderBy );
		foreach ( $order_arr as $order )
		{
			if(!$order)
				continue;
			
			list ( $field_name , $ascending ) = baseObjectFilter::getFieldAndDirection($order);
			$orderObject[$field_name] = $ascending;
		}
		
		return $orderObject;
	}

}