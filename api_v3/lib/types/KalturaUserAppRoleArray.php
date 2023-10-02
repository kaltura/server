<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserAppRoleArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaUserAppRole");
	}
	
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUserAppRoleArray();
		if ($arr == null)
		{
			return $newArr;
		}
		
		foreach($arr as $obj)
		{
			$nObj = new KalturaUserAppRole();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
}

