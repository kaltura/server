<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBaseEntryCloneOptionItem extends KalturaObject
{
    /**
     * The clone option
     *
     * @var KalturaBaseEntryCloneOptions
     */
    public $option;
    /**
     * The type of the condition
     *
     * @var KalturaResponseProfileType
     */
    public $type;

    /* (non-PHPdoc)
     * @see KalturaObject::toObject()
     */
    public function toObject($dbObject = null, $skip = array())
    {
        return $this->option;
    }

    private static $mapBetweenObjects = array
    (
        'option',
        'type',
    );

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
    }
}