<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserAgentRestriction extends KalturaBaseRestriction 
{
	/**
	 * User agent restriction type (Allow or deny)
	 * 
	 * @var KalturaUserAgentRestrictionType
	 */
	public $userAgentRestrictionType; 
	
	/**
	 * A comma seperated list of user agent regular expressions
	 * 
	 * @var string
	 */
	public $userAgentRegexList;
	
	private static $mapBetweenObjects = array
	(
		"userAgentRestrictionType" => "type",
		"userAgentRegexList",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}