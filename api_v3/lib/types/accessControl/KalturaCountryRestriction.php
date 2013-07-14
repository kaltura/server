<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaRule instead
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
		"countryRestrictionType",
		"countryList",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseRestriction::toRule()
	 */
	public function toRule(KalturaRestrictionArray $restrictions)
	{
		return $this->toObject(new kAccessControlCountryRestriction());
	}
}