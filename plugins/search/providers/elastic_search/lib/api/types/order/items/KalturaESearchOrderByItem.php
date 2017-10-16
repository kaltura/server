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

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }
    
}
