<?php
/**
 * @package plugins.fileSync
 * @subpackage api.filters
 */
class KalturaFileSyncFilter extends KalturaFileSyncBaseFilter
{
	static private $map_between_objects = array
	(
		"fileObjectTypeEqual" => "_eq_object_type",
		"fileObjectTypeIn" => "_in_object_type",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
