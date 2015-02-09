<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDestFileSyncDescriptorArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaDestFileSyncDescriptorArray();
		if ($arr == null)
			return $newArr;
		foreach ($arr as $obj)
		{
    		$nObj = new KalturaDestFileSyncDescriptor();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaDestFileSyncDescriptor");	
	}
}
