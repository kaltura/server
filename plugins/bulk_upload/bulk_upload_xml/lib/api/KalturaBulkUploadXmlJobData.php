<?php

/**
 * 
 * Represents the Bulk upload job data for xml bulk upload
 * @author Roni
 *
 */
class KalturaBulkUploadXmlJobData extends KalturaBulkUploadJobData
{
	/**
	 * Created by the API
	 * 
	 * @var string
	 */
	public $xmlFilePath;

	/**
	 * 
	 * Maps between objects and the properties
	 * @var array
	 */
	private static $map_between_objects = array
	(
		"xmlFilePath"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kBulkUploadJobData();
			
		return parent::toObject($dbData);
	}
}