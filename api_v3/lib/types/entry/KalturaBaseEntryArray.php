<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBaseEntryArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null, $isAdmin = false)
	{
		$newArr = new KalturaBaseEntryArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = KalturaEntryFactory::getInstanceByType($obj->getType(), $isAdmin);
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaBaseEntry");	
	}
}