<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerAkamaiHttp extends KalturaUrlTokenizer {

	/**
	 * param
	 *
	 * @var string
	 */
	public $paramName;
	
	/**
	 * @var string
	 */
	public $rootDir;
	
	private static $map_between_objects = array
	(
			"paramName",
			"rootDir",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kAkamaiHttpUrlTokenizer();
			
		parent::toObject($dbObject, $skip);
	
		return $dbObject;
	}
}
