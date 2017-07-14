<?php
/**
 * Created by IntelliJ IDEA.
 * User: moshe.maor
 * Date: 7/16/2017
 * Time: 10:51 PM
 */
require 'vendor/autoload.php';

class BeaconElasticClient
{
    const HOST='127.0.0.1:9200';
    const INDEX_NAME = "beaconindex";

    function __construct()
    {
        $this->client =  Elasticsearch\ClientBuilder::create()->setHosts(array(self::HOST))->build();
    }
    function index($indexType,$mappingArray,$ttl=3600)
    {
        $params =   ['index'=>self::INDEX_NAME ,
                    'type' => $indexType ,
                    'ttl' => $ttl."S" ,
                    'body' => $mappingArray];

        $ret =  $this->client->index($params);
        return $ret;
    }
    function search($indexType,$searchParamsArray,$pageSize , $pageIndex)
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
            'index'=>self::INDEX_NAME ,
            'type' => $indexType ,
            'body' => [
                'query' => [
                     'bool'=> [
                        'must' =>
                            [
                                $query
                            ]
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

class BeaconObject
{

    function __construct ($partnerId,$params)
    {
        $this->content = $params;
        //open connection to elastic server
        $this->client = new BeaconElasticClient();
    }

    function indexObjectState()
    {
        $this->client->index("State" , $this->content);
    }

    function log($ttl)
    {
        $this->client->index("log" , $this->content ,3600);
    }

    function searchObject($pageSize,$pageIndex)
    {
        return $this->client->search("State", $this->content,$pageSize,$pageIndex);
    }
    function search($param,$pageSize,$pageIndex)
    {
        $param = array_merge($this->content,$param);
        return $this->client->search("log",$param,$pageSize,$pageIndex);
    }

}