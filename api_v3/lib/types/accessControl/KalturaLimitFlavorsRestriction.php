<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaRule instead
 */
class KalturaLimitFlavorsRestriction extends KalturaBaseRestriction 
{
	/**
	 * Limit flavors restriction type (Allow or deny)
	 * 
	 * @var KalturaLimitFlavorsRestrictionType
	 */
	public $limitFlavorsRestrictionType; 
	
	/**
	 * Comma separated list of flavor params ids to allow to deny 
	 * 
	 * @var string
	 */
	public $flavorParamsIds;
	
	private static $mapBetweenObjects = array
	(
		"limitFlavorsRestrictionType",
		"flavorParamsIds",
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
		return $this->toObject(new kAccessControlLimitFlavorsRestriction());
	}
}