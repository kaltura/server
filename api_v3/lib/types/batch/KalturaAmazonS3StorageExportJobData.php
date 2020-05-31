<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAmazonS3StorageExportJobData extends KalturaStorageExportJobData 
{
	/**
	 * @var KalturaAmazonS3StorageProfileFilesPermissionLevel
	 */   	
    public $filesPermissionInS3;   
    
	/**
	 * @var string
	 */   	
    public $s3Region;   
	
	/**
	 * @var string
	 */   	
    public $sseType;   
	
	/**
	 * @var string
	 */   	
    public $sseKmsKeyId;   
    
	/**
	 * @var string
	 */   	
    public $signatureType;   
    
    	/**
	 * @var string
	 */   	
    public $endPoint;

    /**
	 * @var string
	 */
	public $storageClass;

	private static $map_between_objects = array
	(
		"filesPermissionInS3",	
		"s3Region",	
		"sseType",
		"sseKmsKeyId",
		"signatureType",
		"endPoint",
		"storageClass",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kAmazonS3StorageExportJobData();
			
		return parent::toObject($dbData);
	}
	
}