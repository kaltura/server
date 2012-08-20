<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBaseEntryArray extends KalturaTypedArray
{
	public static function fromEntryArray ( $arr, $isAdmin = false )
	{
		$newArr = new KalturaBaseEntryArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = KalturaEntryFactory::getInstanceByType($obj->getType(), $isAdmin);
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaBaseEntry");	
	}
}