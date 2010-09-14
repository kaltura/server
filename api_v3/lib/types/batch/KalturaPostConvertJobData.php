<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaPostConvertJobData extends KalturaConvartableJobData
{
	/**
	 * @var string
	 */
	public $flavorAssetId;
	
	/**
	 * Indicates if a thumbnail should be created
	 * 
	 * @var bool
	 */
	public $createThumb;
	
	/**
	 * The path of the created thumbnail
	 *  
	 * @var string
	 */
	public $thumbPath;
	
	/**
	 * The position of the thumbnail in the media file
	 *  
	 * @var int
	 */
	public $thumbOffset;
	
	/**
	 * The height of the movie, will be used to comapare if this thumbnail is the best we can have
	 *  
	 * @var int
	 */
	public $thumbHeight;
	
	/**
	 * The bit rate of the movie, will be used to comapare if this thumbnail is the best we can have
	 *  
	 * @var int
	 */
	public $thumbBitrate;
		
	private static $map_between_objects = array
	(
		"flavorAssetId" ,
		"createThumb" ,
		"thumbPath" ,
		"thumbOffset" ,
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
			$dbData = new kPostConvertJobData();
			
		return parent::toObject($dbData);
	}
}

?>