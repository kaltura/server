<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaUltraSearchResult extends KalturaObject {

    /**
     * @var KalturaBaseEntry
     */
    public $entry;

    /**
     * @var string
     */
    public $itemData;

    private static $map_between_objects = array(
        'entry',
        'itemData',
    );

    protected function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

}
