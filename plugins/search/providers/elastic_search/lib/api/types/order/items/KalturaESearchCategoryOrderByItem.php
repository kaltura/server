<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCategoryOrderByItem extends KalturaESearchOrderByItem
{
    /**
     *  @var KalturaESearchCategoryOrderByFieldName
     */
    public $sortField;

    private static $map_between_objects = array(
        'sortField',
    );

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

    public function toObject($object_to_fill = null, $props_to_skip = array())
    {
        if (!$object_to_fill)
            $object_to_fill = new ESearchCategoryOrderByItem();
        return parent::toObject($object_to_fill, $props_to_skip);
    }
}
