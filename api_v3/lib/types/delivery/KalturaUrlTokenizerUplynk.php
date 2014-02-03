<?php
/**
 * @package api
 * @subpackage objects
 * @_!! TODO Plugin
 */
class KalturaUrlTokenizerUplynk extends KalturaUrlTokenizer {
	
	/**
	 * accountId
	 *
	 * @var string
	 */
	public $accountId;
	
	private static $map_between_objects = array
	(
			"accountId",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kUrlTokenizerUplynk();
			
		parent::toObject($dbObject, $skip);
	
		return $dbObject;
	}
}
