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
			if(!empty($value))
			{
				$query[] = array('match'=>[$key=>$value]);
			}
		}
		
		$sort = array();
		foreach ($orderObject as $field_name => $ascending) {
			if($ascending)
				$sort[$field_name] = array('order' => 'asc');
			else
				$sort[$field_name] = array('order' => 'desc');
		}
		
		$params =   [
			'index' => $indexName,
			'type' => $indexType,
			'body' => [
				'size' => $pageSize ,
				'from' => $pageIndex,
				'query' => [
					'bool'=> [
						'must' => $query
					]
				]
			]
		];
		
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