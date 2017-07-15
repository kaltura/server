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

    private function prepareParams($indexType,$mappingArray,$ttl=null)
    {

        $params =   ['index'=>self::INDEX_NAME ,
            'type' => $indexType ,
            'body' => $mappingArray];

        if(!is_null($ttl))
        $params['ttl'] = $ttl."S";

        return $params;
    }

    function index($indexType,$mappingArray,$ttl=3600)
    {
        $params = $this-> prepareParams($indexType,$mappingArray,$ttl);
        $ret =  $this->client->index($params);
        return $ret;
    }

    function update($indexType,$mappingArray,$id)
    {
        $params = $this-> prepareParams($indexType,$mappingArray);
        $params['id']=$id;
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
                    'size' => $pageSize ,
                    'from' => $pageIndex,
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
    /**
     * @var KalturaBeaconObjectTypes
     */
    public $relatedObjectType;

    /**
     * @var string
     */
    public $eventType;

    /**
     * @var string
     */
    public $objectId;

    /**
     * @var string
     */
    public $privateData;

    function __construct ($partnerId,array $params)
    {
        $this->content              = $params;
        $this->content['partnerId'] = $partnerId;
        $this->client               = new BeaconElasticClient();
    }

    function indexObjectState($id)
    {
        $this->client->update("State",$this->content,$id);
    }

    function log($ttl)
    {
        $this->client->index("log",$this->content,3600);
    }

    function searchObject($pageSize,$pageIndex)
    {
        return $this->client->search("State", $this->content,$pageSize,$pageIndex);
    }
    function search($pageSize,$pageIndex)
    {
        return $this->client->search("log",$this->content,$pageSize,$pageIndex);
    }

}