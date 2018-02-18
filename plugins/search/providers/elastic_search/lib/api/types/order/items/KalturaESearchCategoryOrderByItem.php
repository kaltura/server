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

    private static $map_field_enum = array(
        KalturaESearchCategoryOrderByFieldName::UPDATED_AT => ESearchCategoryOrderByFieldName::UPDATED_AT,
        KalturaESearchCategoryOrderByFieldName::CREATED_AT => ESearchCategoryOrderByFieldName::CREATED_AT,
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

    public function getFieldEnumMap()
    {
        return self::$map_field_enum;
    }

    public function getItemFieldName()
    {
        return $this->sortField;
    }

}
