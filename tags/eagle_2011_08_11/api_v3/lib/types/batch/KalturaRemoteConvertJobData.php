<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaRemoteConvertJobData extends KalturaConvartableJobData
{
	/**
	 * @var string
	 */
	public $srcFileUrl;
	
	/**
	 * Should be set by the API
	 * 
	 * @var string
	 */
	public $destFileUrl;


	private static $map_between_objects = array
	(
		"srcFileUrl" ,
		"destFileUrl" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kRemoteConvertJobData();
			
		return parent::toObject($dbData);
	}
}

?>