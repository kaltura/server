<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaStorageJobData extends KalturaJobData
{
	/**
	 * @var string
	 */   	
    public $serverUrl; 

	/**
	 * @var string
	 */   	
    public $serverUsername; 

	/**
	 * @var string
	 */   	
    public $serverPassword;

	/**
	 * @var bool
	 */   	
    public $ftpPassiveMode;

	/**
	 * @var string
	 */   	
    public $srcFileSyncLocalPath;


	/**
	 * @var string
	 */   
	public $srcFileSyncId;
	
	private static $map_between_objects = array
	(
	    "serverUrl" , 
	    "serverUsername" , 
	    "serverPassword" ,
	    "ftpPassiveMode" ,
	    "srcFileSyncLocalPath" ,
		"srcFileSyncId" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kStorageJobData();
			
		return parent::toObject($dbData);
	}
}

?>