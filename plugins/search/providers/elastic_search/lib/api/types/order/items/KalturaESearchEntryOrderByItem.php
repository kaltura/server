<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchEntryOrderByItem extends KalturaESearchOrderByItem
{
    /**
     *  @var KalturaESearchEntryOrderByFieldName
     */
    public $sortField;

    private static $map_between_objects = array(
        'sortField',
    );

    private static $map_field_enum = array(
        KalturaESearchEntryOrderByFieldName::CREATED_AT => ESearchEntryOrderByFieldName::CREATED_AT,
        KalturaESearchEntryOrderByFieldName::UPDATED_AT => ESearchEntryOrderByFieldName::UPDATED_AT,
        KalturaESearchEntryOrderByFieldName::END_DATE => ESearchEntryOrderByFieldName::END_DATE,
        KalturaESearchEntryOrderByFieldName::START_DATE => ESearchEntryOrderByFieldName::START_DATE,
        KalturaESearchEntryOrderByFieldName::NAME => ESearchEntryOrderByFieldName::NAME,
        KalturaESearchEntryOrderByFieldName::VIEWS => ESearchEntryOrderByFieldName::VIEWS,
        KalturaESearchEntryOrderByFieldName::VOTES => ESearchEntryOrderByFieldName::VOTES,
        KalturaESearchEntryOrderByFieldName::PLAYS => ESearchEntryOrderByFieldName::PLAYS,
        KalturaESearchEntryOrderByFieldName::LAST_PLAYED_AT => ESearchEntryOrderByFieldName::LAST_PLAYED_AT,
    );

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

    public function toObject($object_to_fill = null, $props_to_skip = array())
    {
        if (!$object_to_fill)
            $object_to_fill = new ESearchEntryOrderByItem();
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
