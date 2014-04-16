<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerLimeLight extends KalturaUrlTokenizer {

	private static $map_between_objects = array
	(
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kLimeLightUrlTokenizer();
			
		parent::toObject($dbObject, $skip);
	
		return $dbObject;
	}
}
