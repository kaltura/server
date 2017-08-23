<?php

/**
 * @package plugins.beacon
 * @subpackage api.filters
 */
class KalturaBeaconFilter extends KalturaBeaconBaseFilter
{
	/**
	 * @var KalturaBeaconIndexType
	 */
	public $indexTypeEqual;
	
	public function getCoreFilter()
	{
		return null;
	}
	
	public function getListResponse(KalturaFilterPager $pager)
	{
		$searchObject = $this->createSearchObject();
		
		$searchMgr = new kBeaconSearchManger();
		$searchQuery = $searchMgr->buildSearchQuery(kBeacon::ELASTIC_BEACONS_INDEX_NAME, $this->indexTypeEqual, $searchObject, $pager->pageSize, $pager->calcOffset());
		$responseArray = $searchMgr->search($searchQuery);
		$responseArray = $searchMgr->getHitsFromElasticResponse($responseArray);
		
		$response = new KalturaBeaconListResponse();
		$response->objects = KalturaBeaconArray::fromDbArray($responseArray);
		return $response;
	}
	
	protected function createSearchObject()
	{
		$searchObject = array();
		
		$searchObject['terms'] = $this->getSearchTerms();
		$searchObject['range'] = $this->getSearchRangeTerms();
		$searchObject['order'] = $this->getOrderByObject();
		
		return $searchObject;
	}
	
	private function getSearchTerms()
	{
		$terms = array();
		
		$terms[kBeacon::FIELD_RELATED_OBJECT_TYPE] = $this->relatedObjectTypeIn;
		$terms[kBeacon::FIELD_OBJECT_ID] = $this->objectIdIn;
		$terms[kBeacon::FIELD_EVENT_TYPE] = $this->eventTypeIn;
		$terms[kBeacon::FIELD_PARTNER_ID] = kCurrentContext::getCurrentPartnerId();
		
		return $terms;
	}
	
	private function getSearchRangeTerms()
	{
		$range = array();
		$range[kBeacon::FIELD_CREATED_AT]['gte'] = $this->createdAtGreaterThanOrEqual;
		$range[kBeacon::FIELD_CREATED_AT]['lte'] = $this->createdAtLessThanOrEqual;
		
		$range[kBeacon::FIELD_CREATED_AT]['gte'] = $this->updatedAtGreaterThanOrEqual;
		$range[kBeacon::FIELD_CREATED_AT]['lte'] = $this->updatedAtLessThanOrEqual;
		
		return $range;
	}
	
	private function getOrderByObject()
	{
		if (!$this->orderBy)
			return array();
		
		$orderObject = array();
		$orderByMap = $this->getOrderByMap();
		
		$order_arr = explode(",", $this->orderBy);
		foreach ($order_arr as $order) 
		{
			if (!$order || !isset($orderByMap[$order]))
				continue;
			
			list ($field_name, $ascending) = baseObjectFilter::getFieldAndDirection($order);
			$orderObject[$field_name] = $ascending;
		}
		
		return $orderObject;
	}
}