<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchRange extends KalturaObject
{
    /**
     * @var int
     */
    public $greaterThanOrEqual;

    /**
     * @var int
     */
    public $lessThanOrEqual;

    /**
     * @var int
     */
    public $greaterThan;

    /**
     * @var int
     */
    public $lessThan;

    private static $mapBetweenObjects = array
    (
        "greaterThanOrEqual",
        "lessThanOrEqual",
        "greaterThan",
        "lessThan",
    );

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
    }

    public function toObject($object_to_fill = null, $props_to_skip = array())
    {
        if (!$object_to_fill)
            $object_to_fill = new ESearchRange();
        return parent::toObject($object_to_fill, $props_to_skip);
    }
}
