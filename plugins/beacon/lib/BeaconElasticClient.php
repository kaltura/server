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
	
	public function search($indexName, $indexType, $searchParamsArray, $pageSize ,$pageIndex)
	{
		$query = array ();
		foreach($searchParamsArray as $key => $value)
		{
			if(!empty($value))
			{
				$query[]=array('match'=>[$key=>$value]);
			}
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
		
		$response = $this->client->search($params);
		$ret = array();
		foreach($response['hits']['hits'] as $item)
		{
			$ret[]=$item['_source'];
		}
		return $ret;
	}
}