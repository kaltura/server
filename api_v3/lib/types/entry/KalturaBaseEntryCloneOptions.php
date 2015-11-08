<?php

/**
 * A representation of a live stream recording entry configuration
 *
 * @package api
 * @subpackage objects
 */
class KalturaBaseEntryCloneOptions extends KalturaObject
{
    /**
     * @var KalturaNullableBoolean
     */
    public $shouldCopyEntitlement;


    private static $map_between_objects = array
    (
        "shouldCopyEntitlement",
    );

    public function getMapBetweenObjects ( )
    {
        return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
    }




}