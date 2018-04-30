<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchOrderByItem extends KalturaObject
{
    /**
     *  @var KalturaESearchSortOrder
     */
    public $sortOrder;

    private static $map_between_objects = array(
        'sortOrder',
    );

    abstract public function getFieldEnumMap();

    abstract public function getItemFieldName();

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

    public function toObject($object_to_fill = null, $props_to_skip = array())
    {
        $fieldEnumMap = $this->getFieldEnumMap();
        if(isset($fieldEnumMap[$this->getItemFieldName()]))
        {
            $coreFieldName = $fieldEnumMap[$this->getItemFieldName()];
            $object_to_fill->setSortField($coreFieldName);
            $props_to_skip[] = 'sortField';
        }

        return parent::toObject($object_to_fill, $props_to_skip);
    }
    
}
