<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaConvertJobData extends KalturaConvartableJobData
{
	/**
	 * @var string
	 */
	public $destFileSyncLocalPath;
	
	/**
	 * @var string
	 */
	public $destFileSyncRemoteUrl;
	
	/**
	 * @var string
	 */
	public $logFileSyncLocalPath;
	
	
	/**
	 * @var string
	 */
	public $flavorAssetId;
	
	
	/**
	 * @var string
	 */
	public $remoteMediaId;
    
	private static $map_between_objects = array
	(
		"destFileSyncLocalPath" ,
		"destFileSyncRemoteUrl" ,
		"flavorAssetId" ,
		"remoteMediaId" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kConvertJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
	
	/**
	 * @param string $subType
	 * @return int
	 */
	public function toSubType($subType)
	{
		return $this->toDynamicEnumValue('KalturaConversionEngineType', $subType);
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		return $this->fromDynamicEnumValue('KalturaConversionEngineType', $subType);
	}
}
