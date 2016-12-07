<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFairPlayEntryPlayingPluginData extends KalturaDrmEntryPlayingPluginData {

    /**
     * @var string
     */
    public $certificate;

    private static $map_between_objects = array(
        'certificate',
    );

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

}