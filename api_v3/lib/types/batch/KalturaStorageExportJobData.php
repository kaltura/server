<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaStorageExportJobData extends KalturaStorageJobData
{

	/**
	 * @var string
	 */   	
    public $destFileSyncStoredPath;
    
	/**
	 * @var bool
	 */   	
    public $force;
	
    
    
	private static $map_between_objects = array
	(
	    "destFileSyncStoredPath" ,
	    "force" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kStorageExportJobData();
			
		return parent::toObject($dbData);
	}
}

?>