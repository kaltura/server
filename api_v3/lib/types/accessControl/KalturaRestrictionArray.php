<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaRuleArray instead
 */
class KalturaRestrictionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaRestrictionArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = self::getInstanceByDbObject($obj);
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}

	static function getInstanceByDbObject(kAccessControlRestriction $dbObject)
	{
		$objectClass = get_class($dbObject);
		switch($objectClass)
		{
			case "kAccessControlSiteRestriction":
				return new KalturaSiteRestriction();
			case "kAccessControlCountryRestriction":
				return new KalturaCountryRestriction();
			case "kAccessControlSessionRestriction":
				return new KalturaSessionRestriction();
			case "kAccessControlPreviewRestriction":
				return new KalturaPreviewRestriction();
			case "kAccessControlIpAddressRestriction":
				return new KalturaIpAddressRestriction();
			case "kAccessControlUserAgentRestriction":
				return new KalturaUserAgentRestriction();
			case "kAccessControlLimitFlavorsRestriction":
				return new KalturaLimitFlavorsRestriction();
			default:
				KalturaLog::err("Access control rule type [$objectClass] could not be loaded");
				return null;
		}
	}
	
	public function __construct()
	{
		parent::__construct("KalturaBaseRestriction");	
	}
}