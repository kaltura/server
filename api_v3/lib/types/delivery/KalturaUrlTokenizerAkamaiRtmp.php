<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerAkamaiRtmp extends KalturaUrlTokenizer {

	/**
	 * profile
	 *
	 * @var string
	 */
	public $profile;
	
	/**
	 * Type
	 *
	 * @var string
	 */
	public $type;
	
	/**
	 * @var string
	 */
	public $aifp;
	
	/**
	 * @var bool
	 */
	public $usePrefix;
	
	private static $map_between_objects = array
	(
			"profile",
			"type",
			"aifp",
			"usePrefix",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kAkamaiRtmpUrlTokenizer();
			
		parent::toObject($dbObject, $skip);
	
		return $dbObject;
	}
}
