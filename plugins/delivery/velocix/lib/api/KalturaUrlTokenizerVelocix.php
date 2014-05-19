<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerVelocix extends KalturaUrlTokenizer {
	
	/**
	 * hdsPaths
	 *
	 * @var string
	 */
	public $hdsPaths;
	
	/**
	 * tokenParamName
	 *
	 * @var string
	 */
	public $paramName;
	
	private static $map_between_objects = array
	(
			"hdsPaths",
			"paramName"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kVelocixUrlTokenizer();
			
		parent::toObject($dbObject, $skip);
	
		return $dbObject;
	}
}
