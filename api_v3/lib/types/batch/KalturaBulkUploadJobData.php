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
   
	/**
	 * 
	 * The bulk upload file path
	 * @var string
	 */
	public $filePath;
	
	private static $map_between_objects = array
	(
		"userId",
		"uploadedBy",
		"conversionProfileId",
		"resultsFileLocalPath",
		"resultsFileUrl",
		"numOfEntries",
		"filePath"
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

	/**
	 * @param string $subType is the bulk upload sub type
	 * @return int
	 */
	public function toSubType($subType)
	{
		return kPluginableEnumsManager::apiToCore('BulkUploadType', $subType);
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		return kPluginableEnumsManager::coreToApi('BulkUploadType', $subType);
	}
}