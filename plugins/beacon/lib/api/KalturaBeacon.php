<?php

/**
 * @package api
 * @subpackage objects
 */

class KalturaBeacon extends KalturaObject{

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

}