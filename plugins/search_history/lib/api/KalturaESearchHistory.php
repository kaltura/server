<?php
/**
 * @package plugins.searchHistory
 * @subpackage api.objects
 */
class KalturaESearchHistory extends KalturaObject
{
    /**
     * @readonly
     * @var string
     */
    public $searchTerm;

    /**
     * @readonly
     * @var string
     */
    public $searchedObject;

    /**
     * @readonly
     * @var int
     */
    public $timestamp;

    private static $map_between_objects = array(
        'searchTerm',
        'searchedObject',
        'timestamp'
    );

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

    public function toObject($object_to_fill = null, $props_to_skip = array())
    {
        if (!$object_to_fill)
            $object_to_fill = new ESearchHistory();
        return parent::toObject($object_to_fill, $props_to_skip);
    }

}
