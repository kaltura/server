<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchEntryResult extends KalturaESearchResult {
	
    private static $map_between_objects = array();

    protected function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

	public function getAPIObject($srcObj)
	{
		$isAdmin = kCurrentContext::$ks_object->isAdmin();
		return KalturaEntryFactory::getInstanceByType($srcObj->getObject()->getType(), $isAdmin);
	}



}
