<?php

class KalturaCaptureSpaceUpdateResponseInfo extends KalturaObject {
    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $hash;


    private static $map_between_objects = array (
        "url",
        "hash",
    );

    public function getMapBetweenObjects ( )
    {
        return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
    }
}
