<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchResult extends KalturaObject {

    /**
     * @var KalturaBaseEntry
     */
    public $entry;

    /**
     * @var KalturaESearchItemDataArray
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

    protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
    {
	    $isAdmin = kCurrentContext::$ks_object->isAdmin();
	    $entry = KalturaEntryFactory::getInstanceByType($srcObj->getEntry()->getType(), $isAdmin);

	    $entry->fromObject($srcObj->getEntry());
	    $this->entry = $entry;

	    return parent::doFromObject($srcObj, $responseProfile);
    }


}
