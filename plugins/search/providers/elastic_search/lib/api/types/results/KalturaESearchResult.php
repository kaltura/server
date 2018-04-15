<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchResult extends KalturaObject
{
    /**
     * @var KalturaESearchHighlightArray
     */
    public $highlight;

    /**
     * @var KalturaESearchItemDataResultArray
     */
    public $itemsData;

    private static $map_between_objects = array(
        'highlight',
        'itemsData',
    );

    protected function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

}
