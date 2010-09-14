<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaStorageDeleteJobData extends KalturaStorageJobData
{
	private static $map_between_objects = array
	(
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kStorageDeleteJobData();
			
		return parent::toObject($dbData);
	}
}

?>