<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaEntryVendorTaskArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaEntryVendorTaskArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			$object = new KalturaEntryVendorTask();
			$object->fromObject($obj, $responseProfile);
			$newArr[] = $object;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaEntryVendorTask");
	}
}