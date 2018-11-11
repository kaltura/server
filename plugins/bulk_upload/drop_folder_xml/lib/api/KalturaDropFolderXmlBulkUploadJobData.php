<?php

/**
 *
 * Represents the Bulk upload job data for drop folder xml bulk upload
 * @package plugins.dropFolderXmlBulkUpload
 * @subpackage api.objects
 *
 */
class KalturaDropFolderXmlBulkUploadJobData extends KalturaBulkUploadXmlJobData
{

	/**
	 * the job drop folder id
	 * @var int
	 */
	public $dropFolderId;

	/**
	 *
	 * Maps between objects and the properties
	 * @var array
	 */
	private static $map_between_objects = array
	("dropFolderId");


	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbData = null, $props_to_skip = array())
	{
		if(is_null($dbData))
			$dbData = new kDropFolderBulkUploadXmlJobData();

		return parent::toObject($dbData);
	}
}