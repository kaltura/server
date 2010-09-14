<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaImportMetadataJobData extends KalturaJobData
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
	 * @var int
	 */
	public $metadataId;
    
	private static $map_between_objects = array
	(
		"srcFileUrl" ,
		"destFileLocalPath" ,
		"metadataId" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kImportMetadataJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
}

?>