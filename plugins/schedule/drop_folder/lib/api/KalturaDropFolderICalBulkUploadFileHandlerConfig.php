<?php
/**
 * @package plugins.scheduleDropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderICalBulkUploadFileHandlerConfig extends KalturaDropFolderFileHandlerConfig
{
	/**
	 * The type of the events that ill be created by this upload
	 * @var KalturaScheduleEventType
	 */
	public $eventsType = null;
	
	/**
	 * 
	 * Maps between objects and the properties
	 * @var array
	 */
	private static $map_between_objects = array
	(
		"eventsType",
	);

	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new DropFolderICalBulkUploadFileHandlerConfig();
			
		return parent::toObject($dbData);
	}
}