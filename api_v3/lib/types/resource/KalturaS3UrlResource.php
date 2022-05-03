<?php
/**
 * Used to ingest media located in S3 storage,
 *
 * @package api
 * @subpackage objects
 */
class KalturaS3UrlResource extends KalturaUrlResource
{
	
	private static $map_between_objects = array();
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new kS3UrlResource();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
