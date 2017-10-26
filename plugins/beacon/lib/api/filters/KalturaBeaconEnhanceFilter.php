<?php

/**
 * @package plugins.beacon
 * @subpackage api.filters
 */
class KalturaBeaconEnhanceFilter extends KalturaFilter
{
	/**
	 * @var string
	 */
	public $externalElasticQueryObject;
	
	/**
	 * @var KalturaBeaconIndexType
	 */
	public $indexTypeEqual;
	
	public function getCoreFilter()
	{
		return null;
	}
	
	public function enhanceSearch(KalturaFilterPager $pager)
	{
		$utf8Query = utf8_encode($this->externalElasticQueryObject);
		$queryJsonObject = json_decode($utf8Query, true);
		
		if(!$queryJsonObject)
			throw new KalturaAPIException(APIErrors::INTERNAL_SERVERL_ERROR);
		
		if(!isset($queryJsonObject['query']))
			throw new KalturaAPIException(APIErrors::INTERNAL_SERVERL_ERROR);
		
		$searchQuery = array();
		$searchQuery['body']['query']['bool']['must'] = $queryJsonObject['query'];
		$searchQuery['body']['query']['bool']['filter']['term'] = array ("partnerId" => kCurrentContext::getCurrentPartnerId());
		$searchQuery[elasticClient::ELASTIC_INDEX_KEY] = kBeacon::ELASTIC_BEACONS_INDEX_NAME;
		$searchQuery[kESearchQueryManager::BODY_KEY][elasticClient::ELASTIC_SIZE_KEY] = $pager->pageSize;
		$searchQuery[kESearchQueryManager::BODY_KEY][elasticClient::ELASTIC_FROM_KEY] = $pager->calcOffset();
		
		if(isset($queryJsonObject['sort']))
			$searchQuery[kESearchQueryManager::BODY_KEY][kESearchQueryManager::SORT_KEY] = $queryJsonObject['sort'];
		
		if($this->indexTypeEqual)
			$searchQuery[elasticClient::ELASTIC_TYPE_KEY] = $this->indexTypeEqual;
		
		$searchMgr = new kBeaconSearchQueryManger();
		$responseArray = $searchMgr->search($searchQuery);
		$totalCount = $searchMgr->getTotalCount($responseArray);
		$responseArray = $searchMgr->getHitsFromElasticResponse($responseArray);
		
		$response = new KalturaBeaconListResponse();
		$response->objects = KalturaBeaconArray::fromDbArray($responseArray);
		$response->totalCount = $totalCount;
		return $response;
	}
}