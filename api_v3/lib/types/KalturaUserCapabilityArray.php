<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaUserCapabilityArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUserCapabilityArray();
		if ($arr == null)
		{
			return $newArr;
		}
		
		foreach ($arr as $obj)
		{
			//$nObj = new KalturaUserCapability();
			//$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $obj;
		}
		
		return $newArr;
	}
	
//	public function __construct()
//	{
//		parent::__construct("KalturaUserCapability");
//	}
}
