<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCaptureThumbJobData extends KalturaJobData
{
	/**
	 * @var KalturaFileContainer
	 */
	public $fileContainer;
	
	/**
	 * The translated path as used by the scheduler
	 * @var string
	 */
	public $actualSrcFileSyncLocalPath;
	
	/**
	 * @var string
	 */
	public $srcFileSyncRemoteUrl;
	
	/**
	 * @var int
	 */
	public $thumbParamsOutputId;
	
	/**
	 * @var string
	 */
	public $thumbAssetId;
	
	/**
	 * @var string
	 */
	public $srcAssetId;
	
	/**
	 * @var KalturaAssetType
	 */
	public $srcAssetType;
	
	/**
	 * @var string
	 */
	public $thumbPath;
	
	private static $map_between_objects = array
	(
		"fileContainer" ,
		"actualSrcFileSyncLocalPath" ,
		"srcFileSyncRemoteUrl" ,
		"thumbParamsOutputId" ,
		"thumbAssetId" ,
		"srcAssetId" ,
		"srcAssetType" ,
		"thumbPath" ,
	);


	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	    
	public function toObject(  $dbCaptureThumbJobData = null, $props_to_skip = array()) 
	{
		if(is_null($dbCaptureThumbJobData))
			$dbCaptureThumbJobData = new kCaptureThumbJobData();
			
		return parent::toObject($dbCaptureThumbJobData, $props_to_skip);
	}
}
