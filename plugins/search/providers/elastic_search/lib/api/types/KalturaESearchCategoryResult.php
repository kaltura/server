<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCategoryResult extends KalturaESearchResult {
	
    private static $map_between_objects = array();

    protected function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

	protected function getAPIObject($srcObj)
	{
		return new KalturaCategory();
	}

}
