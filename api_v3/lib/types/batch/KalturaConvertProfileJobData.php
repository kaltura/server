<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaConvertProfileJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $inputFileSyncLocalPath;
	
	/**
	 * The height of last created thumbnail, will be used to comapare if this thumbnail is the best we can have
	 *  
	 * @var int
	 */
	public $thumbHeight;
	
	/**
	 * The bit rate of last created thumbnail, will be used to comapare if this thumbnail is the best we can have
	 *  
	 * @var int
	 */
	public $thumbBitrate;
	
	private static $map_between_objects = array
	(
		"inputFileSyncLocalPath" ,
		"thumbHeight" ,
		"thumbBitrate" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kConvertProfileJobData();
			
		return parent::toObject($dbData);
	}
}

?>