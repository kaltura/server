<?php

/**
 * Represents the Bulk upload job data for xml bulk upload
 * @package plugins.bulkUploadCsv
 * @subpackage api.objects
 */
class KalturaBulkUploadCsvJobData extends KalturaBulkUploadJobData
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
	 * The object in process
	 * @var string
	 */
	public $processObject;

	/**
	 * The type of the object in process
	 * @var string
	 */
	public $processObjectType;

	/**
	 * 
	 * Maps between objects and the properties
	 * @var array
	 */
	private static $map_between_objects = array
	(
		'csvVersion',
		'columns',
		'processObject',
		'processObjectType'
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kBulkUploadCsvJobData();
			
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

