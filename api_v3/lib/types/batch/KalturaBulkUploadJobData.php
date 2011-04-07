<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadJobData extends KalturaJobData
{
	//TODO: Roni - maybe add propertires to the blk upload job data.
	
	/**
	 * @var string
	 */
	public $userId;
	
	/**
	 * The screen name of the user
	 * 
	 * @var string
	 */
	public $uploadedBy;
	
	/**
	 * Selected profile id for all bulk entries
	 * 
	 * @var int
	 */
	public $conversionProfileId;
	
	/**
	 * Created by the API
	 * 
	 * @var string
	 */
	public $csvFilePath;
	
	/**
	 * Created by the API
	 * 
	 * @var string
	 */
	public $resultsFileLocalPath;
	
	/**
	 * Created by the API
	 * 
	 * @var string
	 */
	public $resultsFileUrl;
	
	/**
	 * Number of created entries
	 * 
	 * @var int
	 */
	public $numOfEntries;
	
	/**
	 * The version of the csv file
	 * 
	 * @var KalturaBulkUploadCsvVersion
	 */
	public $csvVersion;
    
	private static $map_between_objects = array
	(
		"userId",
		"uploadedBy",
		"conversionProfileId",
		"csvFilePath",
		"resultsFileLocalPath",
		"resultsFileUrl",
		"numOfEntries",
		"csvVersion",
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
