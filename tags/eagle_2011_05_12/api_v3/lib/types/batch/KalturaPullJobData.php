<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPullJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $srcFileUrl;
	
	/**
	 * @var string
	 */
	public $destFileLocalPath;
    
	private static $map_between_objects = array
	(
		"srcFileUrl" ,
		"destFileLocalPath" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kPullJobData();
			
		return parent::toObject($dbData);
	}
}

?>