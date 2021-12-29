<?php

/**
 * @package plugins.OneDrive
 * @subpackage api.objects
 */
class KalturaOneDriveImportJobData extends KalturaDropFolderImportJobData
{
	/**
	 * @var int
	 */
	public $vendorIntegrationId;
	
	/**
	 * @var int
	 */
	public $expiry;
	
	/**
	 * @var string
	 */
	public $itemId;
	
	/**
	 * @var string
	 */
	public $driveId;
	
	
	private static $map_between_objects = array
	(
		'vendorIntegrationId',
		'expiry',
		'itemId',
		'driveId',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbData = null, $props_to_skip = array())
	{
		if (is_null($dbData))
			$dbData = new kOneDriveImportJobData();
		
		return parent::toObject($dbData, $props_to_skip);
	}
}