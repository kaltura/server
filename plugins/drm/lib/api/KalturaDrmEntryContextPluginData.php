<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDrmEntryContextPluginData extends KalturaPluginData{

    /**
     * For the uDRM we give the drm context data which is a json encoding of an array containing the uDRM data
     * for each flavor that is required from this getContextData request.
     *
     * @var string
     */
    public $flavorData;

    private static $map_between_objects = array(
        'flavorData',
    );

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

}