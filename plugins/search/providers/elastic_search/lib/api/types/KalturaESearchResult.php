<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchResult extends KalturaObject {

    /**
     * @var KalturaObject
     */
    public $object;

    /**
     * @var KalturaESearchItemDataArray
     */
    public $itemData;

    private static $map_between_objects = array(
        'object',
        'itemData',
    );

    protected function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }


	protected function setObject($object, $srcObj)
	{
		$object->fromObject($srcObj->getObject());
		$this->object = $object;
	}
	
}
