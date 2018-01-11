<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 */

class KalturaVendorProfielAdminRuleArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaVendorProfileAdminRule");
	}
	
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaRuleArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			$nObj = new KalturaVendorProfileAdminRule();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
}