<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerAkamaiRtsp extends KalturaUrlTokenizer {

	/**
	 * host
	 *
	 * @var string
	 */
	public $host;
	
	/**
	 * Cp-Code
	 * @var int
	 */
	public $cpcode;
	
	private static $map_between_objects = array
	(
			"host",
			"cpcode",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kAkamaiRtspUrlTokenizer();
			
		parent::toObject($dbObject, $skip);
	
		return $dbObject;
	}
}
