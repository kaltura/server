<?php
/**
 * @package plugins.multiCenters
 * @subpackage api.objects
 */
class KalturaFileSyncImportJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $sourceUrl;
	
	/**
	 * @var string
	 */
	public $filesyncId;
	
	/**
	 * @var string
	 */
	public $tmpFilePath;

	/**
	 * @var string
	 */
	public $destFilePath;
	
    
	private static $map_between_objects = array
	(
		"sourceUrl" ,
		"filesyncId" ,
		"tmpFilePath" ,
		"destFilePath" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kFileSyncImportJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
	
}

