<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerWowzaSecureToken extends KalturaUrlTokenizer {
	
	/**
	 * @var string
	 */
	public $paramPrefix;
	
	/**
	 * @var bool
	 */
	public $shouldIncludeClientIp;
	
	/**
	 * @var string
	 */
	public $hashAlgorithm;
	
	private static $map_between_objects = array
	(
			"paramPrefix",
			"shouldIncludeClientIp",
			"hashAlgorithm"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kWowzaSecureTokenUrlTokenizer();
			
		parent::toObject($dbObject, $skip);
	
		return $dbObject;
	}
}
