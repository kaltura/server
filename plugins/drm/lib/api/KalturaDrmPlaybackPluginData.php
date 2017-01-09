<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDrmPlaybackPluginData extends KalturaPluginData{

    /**
     * @var KalturaDrmSchemeName
     */
    public $scheme;

    /**
     * @var string
     */
    public $licenseURL;

    private static $map_between_objects = array(
        'scheme',
        'licenseURL',
    );

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

}