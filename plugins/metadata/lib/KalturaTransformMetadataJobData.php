<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaTransformMetadataJobData extends KalturaJobData
{
	/**
	 * @var string
	 */
	public $srcXslPath;
	
	/**
	 * @var int
	 */
	public $srcVersion;
	
	/**
	 * @var int
	 */
	public $destVersion;
	
	/**
	 * @var string
	 */
	public $destXsdPath;
	
	/**
	 * @var int
	 */
	public $metadataProfileId;
    
	private static $map_between_objects = array
	(
		"srcXslPath" ,
		"srcVersion" ,
		"destVersion" ,
		"metadataProfileId" ,
		"destXsdPath" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kTransformMetadataJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
}

?>