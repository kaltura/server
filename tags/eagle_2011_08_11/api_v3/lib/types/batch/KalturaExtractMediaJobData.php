<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaExtractMediaJobData extends KalturaConvartableJobData
{
	/**
	 * @var string
	 */
	public $flavorAssetId;
	
	private static $map_between_objects = array
	(
		"flavorAssetId" ,
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kExtractMediaJobData();
			
		return parent::toObject($dbData);
	}
}

?>