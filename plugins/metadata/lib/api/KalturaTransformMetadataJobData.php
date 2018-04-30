<?php
/**
 * @package plugins.metadata
 * @subpackage api.objects
 */
class KalturaTransformMetadataJobData extends KalturaJobData
{
	/**
	 * @var KalturaFileContainer
	 */
	public $srcXsl;
	
	/**
	 * @var int
	 */
	public $srcVersion;
	
	/**
	 * @var int
	 */
	public $destVersion;
	
	/**
	 * @var KalturaFileContainer
	 */
	public $destXsd;
	
	/**
	 * @var int
	 */
	public $metadataProfileId;
    
	private static $map_between_objects = array
	(
		"srcXsl" ,
		"srcVersion" ,
		"destVersion" ,
		"metadataProfileId" ,
		"destXsd" ,
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