<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPermissionArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaPermissionArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaPermission();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct('KalturaPermission');	
	}
}
