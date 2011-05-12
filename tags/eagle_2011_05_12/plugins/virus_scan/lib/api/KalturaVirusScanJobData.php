<?php
/**
 * @package plugins.virusScan
 * @subpackage api.objects
 */
class KalturaVirusScanJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $srcFilePath;
	
	/**
	 * @var string
	 */
	public $flavorAssetId;
	
	/**
	 * @var KalturaVirusScanJobResult
	 */
	public $scanResult;
	
	/**
	 * @var KalturaVirusFoundAction
	 */
	public $virusFoundAction;
	
	
	private static $map_between_objects = array
	(
		"srcFilePath" ,
		"flavorAssetId" ,
		"scanResult" ,
		"virusFoundAction",
	);


	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/**
	 * @param string $subType
	 * @return int
	 */
	public function toSubType($subType)
	{
		return kPluginableEnumsManager::apiToCore('VirusScanEngineType', $subType);
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		return kPluginableEnumsManager::coreToApi('VirusScanEngineType', $subType);
	}
}
