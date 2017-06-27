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

    protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
    {
		/*
	    $isAdmin = kCurrentContext::$ks_object->isAdmin();

		KalturaLog::info("qwer");
		
		$obj = $srcObj->getObject();
		if ($obj instanceof entry)
			$object = KalturaEntryFactory::getInstanceByType($srcObj->getObject()->getType(), $isAdmin);
		else if ($obj instanceof category)
			$object = new KalturaCategory();

		$object->fromObject($srcObj->getObject());
	    $this->object = $object;
		*/

	    return parent::doFromObject($srcObj, $responseProfile);
    }


}
