<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaConvertCollectionFlavorData extends KalturaObject
{
	/**
	 * @var string
	 */
	public $flavorAssetId;
	
	/**
	 * @var int
	 */
	public $flavorParamsOutputId;
	
	/**
	 * @var int
	 */
	public $readyBehavior;
	
	/**
	 * @var int
	 */
	public $videoBitrate;
	
	/**
	 * @var int
	 */
	public $audioBitrate;
	
	/**
	 * @var string
	 */
	public $destFileSyncLocalPath;
	
	/**
	 * @var string
	 */
	public $destFileSyncRemoteUrl;
    
	private static $map_between_objects = array
	(
		"flavorAssetId" ,
		"flavorParamsOutputId" ,
		"readyBehavior" ,
		"videoBitrate" ,
		"audioBitrate" ,
		"destFileSyncLocalPath" ,
		"destFileSyncRemoteUrl" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kConvertCollectionFlavorData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
}

?>