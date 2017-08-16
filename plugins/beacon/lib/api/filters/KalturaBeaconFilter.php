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
		$searchQuery = $this->buildSearchQuery(kBeacon::ELASTIC_INDEX_VALUE, kBeacon::ELASTIC_INDEX_TYPE_STATS, $searchObject, $orderObject, $pager->pageSize, $pager->calcOffset());
		return $this->search($searchQuery);
    }

    public function enhanceSearch(KalturaFilterPager $pager)
    {
		$searchObject = $this->createSearchObject();
		$orderObject = $this->getOrderByObject();
		$searchQuery = $this->buildSearchQuery(kBeacon::ELASTIC_INDEX_VALUE, kBeacon::ELASTIC_INDEX_TYPE_LOG, $searchObject, $orderObject, $pager->pageSize, $pager->calcOffset());
		return $this->search($searchQuery);
    }
    
	private function search($searchQuery)
	{
		$elasticClient = new elasticClient(kBeacon::HOST, kBeacon::PORT);
		$responseArray = $elasticClient->search($searchQuery);
		$responseArray = $this->getHitsFromElasticResponse($responseArray);
		
		$response = new KalturaBeaconListResponse();
		$response->objects = KalturaBeaconArray::fromDbArray($responseArray);
		return $response;
	}

    private function createSearchObject()
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
    
	private function getOrderByObject()
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
	
	private function getHitsFromElasticResponse($elasticResponse)
	{
		$ret = array();
		
		if(!isset($elasticResponse['hits']))
			return $ret;
		
		if(!isset($elasticResponse['hits']['hits']))
			return $ret;
		
		foreach($elasticResponse['hits']['hits'] as $hit)
		{
			$ret[]=$hit['_source'];
		}
		
		return $ret;
	}
	
	private function buildSearchQuery($indexName, $indexType, $searchObject, $orderObject = array(), $pageSize ,$pageIndex)
	{
		$query = array ();
		foreach($searchObject as $key => $value)
		{
			if(!$value)
				continue;
			
			$match = array($key => $value);
			$query[] = array( 'match'=> $match );
		}
		
		$sort = array();
		foreach ($orderObject as $field_name => $ascending)
		{
			if($ascending)
				$sort[$field_name] = array('order' => 'asc');
			else
				$sort[$field_name] = array('order' => 'desc');
		}
		
		$params = array();
		$params['index'] = $indexName;
		$params['type'] = $indexType;
		
		$params['body'] = array();
		$params['body']['size'] = $pageSize;
		$params['body']['from'] = $pageIndex;
		
		$params['body']['query'] = array();
		$params['body']['query']['bool'] = array();
		$params['body']['query']['bool']['must'] = $query;
		
		if(count($sort))
		{
			$params['body']['sort'] = $sort;
		}
		
		return $params;
	}
}