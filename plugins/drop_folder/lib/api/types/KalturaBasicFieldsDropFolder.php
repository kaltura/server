<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */

class KalturaBasicFieldsDropFolder extends KalturaObject
{
    /**
     * @var string
     */
    public $description;

    /*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
    private static $map_between_objects = array(
        'description',
    );

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

    public function toObject($dbObject = null, $skip = array())
    {
        if (is_null($dbObject))
        {
            $dbObject = new DropFolder();
        }

        parent::toObject($dbObject, $skip);

        return $dbObject;
    }
}