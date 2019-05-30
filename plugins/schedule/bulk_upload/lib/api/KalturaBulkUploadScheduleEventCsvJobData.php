<?php
/**
 * Represents the Bulk upload job data for CSV bulk upload
 * @package plugins.scheduleBulkUpload
 * @subpackage api.objects
 */

class KalturaBulkUploadScheduleEventCsvJobData extends KalturaBulkUploadScheduleEventJobData
{
	/**
	 * The version of the csv file
	 * @var KalturaBulkUploadCsvVersion
	 * @readonly
	 */
	public $csvVersion = null;
	
	/**
	 * Array containing CSV headers
	 * @var KalturaStringArray
	 */
	public $columns;
	
	/**
	 *
	 * Maps between objects and the properties
	 * @var array
	 */
	private static $map_between_objects = array
	(
		"csvVersion",
		"columns",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbData = null, $props_to_skip = array())
	{
		if(is_null($dbData))
			$dbData = new kBulkUploadScheduleEventCsvJobData();
		
		return parent::toObject($dbData);
	}
	
	public function toInsertableObject($object_to_fill = null , $props_to_skip = array())
	{
		$dbObj = parent::toInsertableObject($object_to_fill, $props_to_skip);
		
		$this->setType();
		
		return $dbObj;
	}
	
	public function setType ()
	{
		$this->type = kPluginableEnumsManager::coreToApi("KalturaBulkUploadType", BulkUploadCsvPlugin::getApiValue(BulkUploadCsvType::CSV));
	}
}