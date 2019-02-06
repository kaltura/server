<?php

/**
 * Represents the Bulk upload job data for iCal bulk upload
 * @package plugins.scheduleBulkUpload
 * @subpackage api.objects
 */
class KalturaBulkUploadICalJobData extends KalturaBulkUploadJobData
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
			$dbData = new kBulkUploadICalJobData();
			
		return parent::toObject($dbData);
	}
	
	public function toInsertableObject($object_to_fill = null , $props_to_skip = array())
	{
	    $dbObj = parent::toInsertableObject($object_to_fill, $props_to_skip);
	    
	    $this->setType();
	    
	    return $dbObj;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert($propertiesToSkip)
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('eventType');
		parent::validateForInsert($propertiesToSkip);
	}
	
	public function setType ()
	{
	    $this->type = kPluginableEnumsManager::coreToApi("KalturaBulkUploadType", BulkUploadSchedulePlugin::getApiValue(BulkUploadScheduleType::ICAL));
	}
}