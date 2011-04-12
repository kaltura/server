<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadJobData extends KalturaJobData
{
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
   
	private static $map_between_objects = array
	(
		"userId",
		"uploadedBy",
		"conversionProfileId",
		"resultsFileLocalPath",
		"resultsFileUrl",
		"numOfEntries",
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