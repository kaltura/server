<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCountryRestriction extends KalturaBaseRestriction 
{
	/**
	 * Country restriction type (Allow or deny)
	 * 
	 * @var KalturaCountryRestrictionType
	 */
	public $countryRestrictionType; 
	
	/**
	 * Comma separated list of country codes to allow to deny 
	 * 
	 * @var string
	 */
	public $countryList;
	
	private static $mapBetweenObjects = array
	(
		"countryRestrictionType" => "type",
		"countryList",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}