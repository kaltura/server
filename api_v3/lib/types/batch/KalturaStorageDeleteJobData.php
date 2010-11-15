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
	
	/**
	 * @param string $subType
	 * @return int
	 */
	public function toSubType($subType)
	{
		// TODO - change to pluginable enum to support more file export protocols
		return $subType;
	}
	
	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		// TODO - change to pluginable enum to support more file export protocols
		return $subType;
	}
}

