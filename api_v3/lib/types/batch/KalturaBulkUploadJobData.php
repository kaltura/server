<?php
/**
 * @package api
 * @subpackage objects
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
	 * Friendly name of the file, used to be recognized later in the logs.
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
	
	/**
	 * Recipients of the email for bulk upload success/failure
	 * @var string
	 */
	public $emailRecipients;
	
	/**
	 * Number of objects that finished on error status
	 * @var int
	 */
	public $numOfErrorObjects;
	
	
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
		"numOfErrorObjects",
	);

	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	

	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($source_object)
	{
	    parent::fromObject($source_object);
	    
	    /* @var $source_object kBulkUploadJobData */
	    $this->objectData = null;
	    switch (get_class($source_object->getObjectData()))
	    {
	        case 'kBulkUploadEntryData':
	            $this->objectData = new KalturaBulkUploadEntryData();
	            break;
	        case 'kBulkUploadCategoryData':
	            $this->objectData = new KalturaBulkUploadCategoryData();
	            break;
	        case 'kBulkUploadCategoryUserData':
	            $this->objectData = new KalturaBulkUploadCategoryUserData();
	            break;
	        case 'kBulkUploadUserData':
	            $this->objectData = new KalturaBulkUploadUserData();
	            break;
	        case 'kBulkUploadCategoryEntryData':
	            $this->objectData = new KalturaBulkUploadCategoryEntryData();
	            break;
	        default:
	            break;
	    }
	    
	    if ($this->objectData)
	    {
	        KalturaLog::debug("Object data class was found: ". get_class($this->objectData));
	        $this->objectData->fromObject($source_object->getObjectData());
	    }
	        
	        
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
	
	public function setType()
	{
	    
	}
}