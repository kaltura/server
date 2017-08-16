<?php
require 'vendor/autoload.php';

class BeaconElasticClient
{
	const HOST='127.0.0.1:9200';
	const INDEX_NAME = "beaconindex";
	
	function __construct()
	{
		$this->client =  Elasticsearch\ClientBuilder::create()->setHosts(array(self::HOST))->build();
	}
	
	public function search($indexName, $indexType, $searchObject, $orderObject = array(), $pageSize ,$pageIndex)
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
		
		$response = $this->client->search($params);
		$ret = array();
		foreach($response['hits']['hits'] as $item)
		{
			$ret[]=$item['_source'];
		}
		return $ret;
	}
}