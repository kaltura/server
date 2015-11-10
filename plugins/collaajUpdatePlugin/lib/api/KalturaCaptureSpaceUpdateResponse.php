<?php

class KalturaCaptureSpaceUpdateResponse extends KalturaObject {
    /**
     * @var KalturaCaptureSpaceUpdateResponseInfo
     */
    public $info;


    private static $map_between_objects = array (
        "info",
    );

    public function getMapBetweenObjects ( )
    {
        return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
    }
}
