<?php

/**
 * @package plugins.beacon
 * @subpackage model
 */
class kBeaconSearchQueryManger
{
	private static $elasticClient = null;
	
	public function __construct()
	{
		if (self::$elasticClient)
			return self::$elasticClient;
		
		$beaconElasticConfig = kConf::get('beacon', 'elastic');
		$host = isset($beaconElasticConfig['elasticHost']) ? $beaconElasticConfig['elasticHost'] : null;
		$port = isset($beaconElasticConfig['elasticPort']) ? $beaconElasticConfig['elasticPort'] : null;
		
		self::$elasticClient = new elasticClient($host, $port);
	}
	
	public function search($searchQuery)
	{
		return self::$elasticClient->search($searchQuery);
	}
	
	public function get($searchQuery)
	{
		return self::$elasticClient->get($searchQuery);
	}
	
	public function delete($deleteQuery)
	{
		return self::$elasticClient->deleteByQuery($deleteQuery);
	}
	
	public function deleteByObjectId($objectId)
	{
		$deleteObject = array();
		$deleteObject['terms'] = array(kBeacon::FIELD_OBJECT_ID => $objectId, kBeacon::FIELD_PARTNER_ID => kCurrentContext::getCurrentPartnerId());
		
		$deleteQuery = $this->buildSearchQuery(kBeacon::ELASTIC_BEACONS_INDEX_NAME, null, $deleteObject);
		return $this->delete($deleteQuery);
	}
	
	public function buildSearchQuery($indexName, $indexType = null, $searchObject, $pageSize = 30, $pageIndex = 1)
	{
		$query = array();
		
		foreach ($searchObject[kESearchQueryManager::TERMS_KEY] as $key => $value)
		{
			if (!$value)
				continue;
			
			$terms = array($key => explode(",",$value));
			$query[] = array(kESearchQueryManager::TERMS_KEY => $terms);
		}
		
		foreach ($searchObject[kESearchQueryManager::RANGE_KEY] as $key => $value)
		{
			if (!$value[kESearchQueryManager::GTE_KEY] && !$value[kESearchQueryManager::LTE_KEY])
				continue;
			
			$range = array();
			
			if($value[kESearchQueryManager::GTE_KEY])
				$range[kESearchQueryManager::GTE_KEY] = $value[kESearchQueryManager::GTE_KEY];
			
			if($value[kESearchQueryManager::LTE_KEY])
				$range[kESearchQueryManager::LTE_KEY] = $value[kESearchQueryManager::LTE_KEY];
			
			$term = array($key => $range);
			$query[] = array(kESearchQueryManager::RANGE_KEY => $term);
		}
		
		
		$sort = array();
		foreach ($searchObject[kESearchQueryManager::ORDER_KEY] as $field_name => $ascending)
		{
			if ($ascending)
				$sort[$field_name] = array(kESearchQueryManager::ORDER_KEY => kESearchQueryManager::ORDER_ASC_KEY);
			else
				$sort[$field_name] = array(kESearchQueryManager::ORDER_KEY => kESearchQueryManager::ORDER_DESC_KEY);
		}
		
		$params = array();
		$params[elasticClient::ELASTIC_INDEX_KEY] = $indexName;
		
		if ($indexType)
			$params[elasticClient::ELASTIC_TYPE_KEY] = $indexType;
		
		$params[kESearchQueryManager::BODY_KEY] = array();
		
		if($pageSize)
			$params[kESearchQueryManager::BODY_KEY][elasticClient::ELASTIC_SIZE_KEY] = $pageSize;
		
		if($pageIndex)
			$params[kESearchQueryManager::BODY_KEY][elasticClient::ELASTIC_FROM_KEY] = $pageIndex;
		
		$params[kESearchQueryManager::BODY_KEY][kESearchQueryManager::QUERY_KEY] = array();
		$params[kESearchQueryManager::BODY_KEY][kESearchQueryManager::QUERY_KEY][kESearchQueryManager::BOOL_KEY] = array();
		$params[kESearchQueryManager::BODY_KEY][kESearchQueryManager::QUERY_KEY][kESearchQueryManager::BOOL_KEY][kESearchQueryManager::FILTER_KEY] = array();
		$params[kESearchQueryManager::BODY_KEY][kESearchQueryManager::QUERY_KEY][kESearchQueryManager::BOOL_KEY][kESearchQueryManager::FILTER_KEY][kESearchQueryManager::BOOL_KEY] = array();
		$params[kESearchQueryManager::BODY_KEY][kESearchQueryManager::QUERY_KEY][kESearchQueryManager::BOOL_KEY][kESearchQueryManager::FILTER_KEY][kESearchQueryManager::BOOL_KEY][kESearchQueryManager::MUST_KEY] = $query;
		
		if (count($sort))
		{
			$params[kESearchQueryManager::BODY_KEY][kESearchQueryManager::SORT_KEY] = $sort;
		}
		
		KalturaLog::debug("Body = " . print_r($params, true));
		
		return $params;
	}
	
	public function getHitsFromElasticResponse($elasticResponse)
	{
		$ret = array();
		
		if (!isset($elasticResponse[kESearchCoreAdapter::HITS_KEY]))
			return $ret;
		
		if (!isset($elasticResponse[kESearchCoreAdapter::HITS_KEY][kESearchCoreAdapter::HITS_KEY]))
			return $ret;
		
		foreach ($elasticResponse[kESearchCoreAdapter::HITS_KEY][kESearchCoreAdapter::HITS_KEY] as $hit)
		{
			$hit['_source']['id'] = $hit['_id'];
			$hit['_source']['indexType'] = $hit['_type'];
			$ret[] = $hit['_source'];
		}
		
		return $ret;
	}
	
	public function getTotalCount($elasticResponse)
	{
		$totalCount = 0;
		
		if (!isset($elasticResponse['hits']))
			return $totalCount;
		
		if (!isset($elasticResponse['hits']['total']))
			return $totalCount;
		
		return $elasticResponse['hits']['total'];
	}
}
