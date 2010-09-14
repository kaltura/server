<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlattenJobData extends KalturaJobData
{
	private static $map_between_objects = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbData = null, $propsToSkip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kFlattenJobData();
			
		return parent::toObject($dbData);
	}
}

?>