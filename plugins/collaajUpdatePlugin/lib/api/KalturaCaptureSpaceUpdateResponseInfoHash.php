<?php

class KalturaCaptureSpaceUpdateResponseInfoHash extends KalturaObject {
    /**
     * @var string
     */
    public $algorithm;

    /**
     * @var string
     */
    public $value;


    private static $map_between_objects = array (
        "algorithm",
        "value",
    );

    public function getMapBetweenObjects ( )
    {
        return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
    }
}
