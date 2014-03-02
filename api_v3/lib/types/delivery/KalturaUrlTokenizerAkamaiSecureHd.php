<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerAkamaiSecureHd extends KalturaUrlTokenizer {

	/**
	 * @var string
	 */
	public $param;
	
	/**
	 * @var string
	 */
	public $aclRegex;

	/**
	 * @var string
	 */
	public $aclPostfix;
	
	private static $map_between_objects = array
	(
			"param",
			"aclRegex",
			"aclPostfix"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kAkamaiSecureHDUrlTokenizer();
			
		parent::toObject($dbObject, $skip);
	
		return $dbObject;
	}
}
