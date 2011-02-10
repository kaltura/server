<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCaptureThumbJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $srcFileSyncLocalPath;
	
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
	 * @var KalturaThumbParamsOutput
	 */
	public $thumbParamsOutput;
	
	/**
	 * @var string
	 */
	public $thumbAssetId;
	
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
		"srcFileSyncLocalPath" ,
		"actualSrcFileSyncLocalPath" ,
		"srcFileSyncRemoteUrl" ,
		"thumbParamsOutputId" ,
		"thumbAssetId" ,
		"srcAssetType" ,
		"thumbPath" ,
	);


	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	    
	/**
	 * @param kCaptureThumbJobData $dbCaptureThumbJobData
	 * @return KalturaCaptureThumbJobData
	 */
	public function fromObject(  $dbCaptureThumbJobData)
	{
		parent::fromObject($dbCaptureThumbJobData);
		
		$dbThumbParams = $dbCaptureThumbJobData->getThumbParamsOutput();
		if($dbThumbParams)
		{
			$this->thumbParamsOutput = KalturaFlavorParamsFactory::getFlavorParamsOutputInstance($dbThumbParams->getType());
			$this->thumbParamsOutput->fromObject($dbThumbParams);
		}
		
		return $this;
	}

	public function toObject(  $dbCaptureThumbJobData = null, $props_to_skip = array()) 
	{
		if(is_null($dbCaptureThumbJobData))
			$dbCaptureThumbJobData = new kCaptureThumbJobData();
			
		if($this->thumbParamsOutput instanceof KalturaThumbParams)
		{
			$dbThumbParams = new thumbParamsOutput();
			$dbThumbParams = $this->thumbParamsOutput->toObject($dbThumbParams);
			$dbCaptureThumbJobData->setThumbParamsOutput($dbThumbParams);
		}
		
		return parent::toObject($dbCaptureThumbJobData, $props_to_skip);
	}
}
