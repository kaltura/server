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
	
	public function buildSearchQuery($indexName, $indexType = null, $searchObject, $pageSize, $pageIndex)
	{
		$query = array();
		
		foreach ($searchObject['terms'] as $key => $value)
		{
			if (!$value)
				continue;
			
			$terms = array($key => explode(",",$value));
			$query[] = array('terms' => $terms);
		}
		
		foreach ($searchObject['range'] as $key => $value)
		{
			if (!$value[baseObjectFilter::GTE] && !$value[baseObjectFilter::LTE])
				continue;
			
			$range = array();
			
			if($value[baseObjectFilter::GTE])
				$range[baseObjectFilter::GTE] = $value[baseObjectFilter::GTE];
			
			if($value[baseObjectFilter::LTE])
				$range[baseObjectFilter::LTE] = $value[baseObjectFilter::LTE];
			
			$term = array($key => $range);
			$query[] = array('range' => $term);
		}
		
		
		$sort = array();
		foreach ($searchObject['order'] as $field_name => $ascending) 
		{
			if ($ascending)
				$sort[$field_name] = array('order' => 'asc');
			else
				$sort[$field_name] = array('order' => 'desc');
		}
		
		$params = array();
		$params['index'] = $indexName;
		
		if ($indexType)
			$params['type'] = $indexType;
		
		$params['body'] = array();
		$params['body']['size'] = $pageSize;
		$params['body']['from'] = $pageIndex;
		
		$params['body']['query'] = array();
		$params['body']['query']['bool'] = array();
		$params['body']['query']['bool']['filter'] = array();
		$params['body']['query']['bool']['filter']['bool'] = array();
		$params['body']['query']['bool']['filter']['bool']['must']= $query;
		
		if (count($sort)) 
		{
			$params['body']['sort'] = $sort;
		}
		
		KalturaLog::debug("Body = " . print_r($params, true));
		
		return $params;
	}
	
	public function getHitsFromElasticResponse($elasticResponse)
	{
		$ret = array();
		
		if (!isset($elasticResponse['hits']))
			return $ret;
		
		if (!isset($elasticResponse['hits']['hits']))
			return $ret;
		
		foreach ($elasticResponse['hits']['hits'] as $hit) 
		{
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
