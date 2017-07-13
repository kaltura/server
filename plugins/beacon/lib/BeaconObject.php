<?php
/**
 * Created by IntelliJ IDEA.
 * User: moshe.maor
 * Date: 7/16/2017
 * Time: 10:51 PM
 */

class BeaconObject
{
    const IndexName = "KalturaBeacons";
    function __construct ($partnerId,$RelatedObjectType, $eventType , $ObjectId ,$privateData)
    {
        $this->content = array(
            "PartnerId" => $partnerId ,
            "RealatedObjectType" => $RelatedObjectType ,
            "EventType" => $eventType ,
            "ObjectId" => $ObjectId,
            "PrivateData" => $privateData);

        //open connection to elastic server
        $this->client = true;//TODO open connection;
    }

    function indexObjectState()
    {
        $indexType = "State";
      //  $this->client->index(self::IndexName ,$indexType , $this->content);
    }

    function log($ttl)
    {
        $this->content['_ttl_'] = $ttl;
        $indexType = "log";
      //  $this->client->index(self::IndexName ,$indexType , $this->content);
    }
}