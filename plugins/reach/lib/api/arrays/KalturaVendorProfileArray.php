<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorProfileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaVendorProfileArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			$object = new KalturaVendorProfile();
			$object->fromObject($obj, $responseProfile);
			$newArr[] = $object;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaVendorProfile");
	}
}