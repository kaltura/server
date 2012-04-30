<?php
/**
 * @package api
 * @subpackage objects
 * @abstract
 */
class KalturaBulkUploadJobData extends KalturaJobData
{
	/**
	 * @var string
	 * @readonly
	 */
	public $userId;
	
	/**
	 * The screen name of the user
	 * @readonly
	 * @var string
	 */
	public $uploadedBy;
	
	/**
	 * Selected profile id for all bulk entries
	 * @deprecated set this parameter on the KalturaBulkUploadEntryData instead
	 * @readonly
	 * @var int
	 */
	public $conversionProfileId;
	
	/**
	 * Created by the API
	 * @readonly
	 * @var string
	 */
	public $resultsFileLocalPath;
	
	/**
	 * Created by the API
	 * @readonly
	 * @var string
	 */
	public $resultsFileUrl;
	
	/**
	 * Number of created entries
	 * @deprecated use numOfObjects instead
	 * @readonly
	 * @var int
	 */
	public $numOfEntries;
	
	/**
	 * 
	 * Number of created objects
	 * @var int
	 * @readonly
	 */
	public $numOfObjects;
   
	/**
	 * 
	 * The bulk upload file path
	 * @var string
	 * @readonly
	 */
	public $filePath;
	
	/**
	 * Type of object for bulk upload
	 * @var KalturaBulkUploadObjectType
	 * @readonly
	 */
	public $bulkUploadObjectType;
	
	/**
	 * Name of the bulk upload file
	 * @var string
	 */
	public $fileName;
	
	/**
	 * Data pertaining to the objects being uploaded
	 * @readonly
	 * @var KalturaBulkUploadObjectData
	 */
	public $objectData;
	
	/**
	 * Type of bulk upload
	 * @var KalturaBulkUploadType
	 * @readonly
	 */
	public $type;
	
	private static $map_between_objects = array
	(
		"userId",
		"uploadedBy",
		"conversionProfileId",
		"resultsFileLocalPath",
		"resultsFileUrl",
		"numOfEntries",
		"numOfObjects",
		"filePath",
		"fileName",
		"bulkUploadObjectType",
	    "objectData",
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
		if(is_null($subType))
			return null;
			
		return kPluginableEnumsManager::apiToCore('BulkUploadType', $subType);
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		if(is_null($subType))
			return null;
			
		return kPluginableEnumsManager::coreToApi('BulkUploadType', $subType);
	}
}