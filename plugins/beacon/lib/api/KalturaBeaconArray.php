<?php

/**
 * @package plugins.beacon
 * @subpackage api.objects
 */
class KalturaBeaconArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaBeaconArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj) 
		{
			$nObj = new KalturaBeacon();
			$nObj->fromArray($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaBeacon");
	}
}