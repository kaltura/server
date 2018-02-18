<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchUserOrderByItem extends KalturaESearchOrderByItem
{
    /**
     *  @var KalturaESearchUserOrderByFieldName
     */
    public $sortField;

    private static $map_between_objects = array(
        'sortField',
    );

    private static $map_field_enum = array(
        KalturaESearchUserOrderByFieldName::CREATED_AT => ESearchUserOrderByFieldName::CREATED_AT,
        KalturaESearchUserOrderByFieldName::UPDATED_AT => ESearchUserOrderByFieldName::UPDATED_AT,
    );

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

    public function toObject($object_to_fill = null, $props_to_skip = array())
    {
        if (!$object_to_fill)
            $object_to_fill = new ESearchUserOrderByItem();
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
