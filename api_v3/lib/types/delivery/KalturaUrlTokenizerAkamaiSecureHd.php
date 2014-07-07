<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerAkamaiSecureHd extends KalturaUrlTokenizer {

	/**
	 * @var string
	 */
	public $paramName;
	
	/**
	 * @var string
	 */
	public $aclPostfix;

	/**
	 * @var string
	 */
	public $customPostfixes;
	
	private static $map_between_objects = array
	(
			"paramName",
			"aclPostfix",
			"customPostfixes"
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
