<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaImportJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $srcFileUrl;
	
	/**
	 * @var string
	 */
	public $destFileLocalPath;
	
	/**
	 * @var string
	 */
	public $flavorAssetId;
    
	private static $map_between_objects = array
	(
		"srcFileUrl" ,
		"destFileLocalPath" ,
		"flavorAssetId" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kImportJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
}

?>