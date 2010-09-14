<?php
class KalturaDirectoryRestriction extends KalturaBaseRestriction 
{
	/**
	 * Kaltura directory restriction type
	 * 
	 * @var KalturaDirectoryRestrictionType
	 */
	public $directoryRestrictionType;
	
	private static $mapBetweenObjects = array
	(
		"directoryRestrictionType" => "type",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}