<?php

class KalturaClearBeaconsJobData extends KalturaJobData
{
	/**
	 * Beacon object Id to clear info for
	 * @var string
	 * @readonly
	 */
	public $objectId;
	
	/**
	 * Beacon object Type to clear info for
	 * @var int
	 */
	public $relatedObjectType;
	
	private static $map_between_objects = array
	(
		"objectId",
		"relatedObjectType",
	);
	
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbData = null, $props_to_skip = array())
	{
		if(is_null($dbData))
			$dbData = new kClearBeconsJobData();
		
		return parent::toObject($dbData);
	}
	
	public function toInsertableObject($object_to_fill = null , $props_to_skip = array())
	{
		$dbObj = parent::toInsertableObject($object_to_fill, $props_to_skip);
		return $dbObj;
	}
}