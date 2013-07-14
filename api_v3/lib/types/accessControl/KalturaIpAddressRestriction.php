<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaRule instead
 */
class KalturaIpAddressRestriction extends KalturaBaseRestriction 
{
	/**
	 * Ip address restriction type (Allow or deny)
	 * 
	 * @var KalturaIpAddressRestrictionType
	 */
	public $ipAddressRestrictionType; 
	
	/**
	 * Comma separated list of ip address to allow to deny 
	 * 
	 * @var string
	 */
	public $ipAddressList;
	
	private static $mapBetweenObjects = array
	(
		"ipAddressRestrictionType",
		"ipAddressList",
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
		return $this->toObject(new kAccessControlIpAddressRestriction());
	}
}