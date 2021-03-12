<?php
/**
 * @package plugins.S3DropFolder
 * @subpackage api.objects
 */
class KalturaS3DropFolderFile extends KalturaDropFolderFile
{
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array(

	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (!$dbObject)
			$dbObject = new S3DropFolderFile();

		return parent::toObject($dbObject, $skip);
	}
}
