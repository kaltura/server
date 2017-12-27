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
		$searchMgr = new kBeaconSearchQueryManger();
		
		$relatedObjectType = $this->relatedObjectTypeEqual;
		if(!$relatedObjectType)
		{
			$relatedObjectTypes = explode(",", $this->relatedObjectTypeIn);
			$relatedObjectType = $relatedObjectTypes[0];
		}
		
		$indexName = kBeacon::ELASTIC_BEACONS_INDEX_NAME;
		$indexType = null;
		if($relatedObjectType && $relatedObjectType != "") 
		{
			$indexName = kBeacon::$indexNameByBeaconObjectType[$relatedObjectType];
			$indexType = kBeacon::$indexTypeByBeaconObjectType[$relatedObjectType];
		}
		
		$searchQuery = $searchMgr->buildSearchQuery($indexName, $indexType, $searchObject, $pager->pageSize, $pager->calcOffset());
		$elasticQueryResponse = $searchMgr->search($searchQuery);
		$responseArray = $searchMgr->getHitsFromElasticResponse($elasticQueryResponse);
		$totalCount = $searchMgr->getTotalCount($elasticQueryResponse);
		
		$response = new KalturaBeaconListResponse();
		$response->objects = KalturaBeaconArray::fromDbArray($responseArray);
		$response->totalCount = $totalCount;
		return $response;
	}
	
	protected function createSearchObject()
	{
		$searchObject = array();
		
		$searchObject[kESearchQueryManager::TERMS_KEY] = $this->getSearchTerms();
		$searchObject[kESearchQueryManager::RANGE_KEY] = $this->getSearchRangeTerms();
		$searchObject[kESearchQueryManager::ORDER_KEY] = $this->getOrderByObject();
		
		return $searchObject;
	}
	
	private function getSearchTerms()
	{
		$terms = array();
		
		$terms[kBeacon::FIELD_OBJECT_ID] = elasticSearchUtils::formatSearchTerm($this->objectIdIn);
		$terms[kBeacon::FIELD_EVENT_TYPE] = elasticSearchUtils::formatSearchTerm($this->eventTypeIn);
		$terms[kBeacon::FIELD_PARTNER_ID] = kCurrentContext::getCurrentPartnerId();
		
		if(isset($this->indexTypeEqual))
			$terms[kBeacon::FIELD_IS_LOG] = ($this->indexTypeEqual == KalturaBeaconIndexType::LOG) ? true : false; 
		
		return $terms;
	}
	
	private function getSearchRangeTerms()
	{
		$range = array();
		
		$range[kBeacon::FIELD_UPDATED_AT][kESearchQueryManager::GTE_KEY] = $this->updatedAtGreaterThanOrEqual;
		$range[kBeacon::FIELD_UPDATED_AT][kESearchQueryManager::LTE_KEY] = $this->updatedAtLessThanOrEqual;
		
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
			
			$order = $orderByMap[$order];
			list ($field_name, $ascending) = baseObjectFilter::getFieldAndDirection($order);
			$orderObject[$field_name] = $ascending;
		}
		
		return $orderObject;
	}
}