<?php

/**
 * @package plugins.beacon
 * @subpackage api.objects
 */

class KalturaBeacon extends KalturaObject{
    const RELATED_OBJECT_TYPE_STRING = 'relatedObjectType';
    const EVENT_TYPE_STRING          = 'eventType'        ;
    const OBJECT_ID_STRING           = 'objectId'         ;
    const PRIVATE_DATA_STRING           = 'privateData'         ;

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


    //Todo add map between objects
    public function indexObjectState()
    {
        $beaconObject = $this->prepareBeaconObject();
        $id = md5($this->relatedObjectType.'_'. $this->eventType.'_'.$this->objectId);
        $ret  = $beaconObject->indexObjectState($id);
        return $ret;
    }

    public function logObjectState($ttl)
    {
        $beaconObject = $this->prepareBeaconObject();
        $ret = $beaconObject->log($ttl);
        return $ret;
    }

    private function prepareBeaconObject()
    {
        $indexObject=array();
        //$indexObject[self::PRIVATE_DATA_STRING] = json_decode($this->privateData,true);
        $indexObject[self::PRIVATE_DATA_STRING] = $this->privateData;
        $indexObject[self::RELATED_OBJECT_TYPE_STRING] = $this->relatedObjectType;
        $indexObject[self::EVENT_TYPE_STRING] = $this->eventType;
        $indexObject[self::OBJECT_ID_STRING] = $this->objectId;
        $beaconObject = new BeaconObject(kCurrentContext::getCurrentPartnerId(),$indexObject);
        return $beaconObject;
    }

    private static $map_between_objects = array(
    'relatedObjectType',
    'eventType',
    'objectId',
    'privateData',
    'partnerId');

    public function getMapBetweenObjects()
    {
        return array_merge(self::$map_between_objects);
    }
}
