<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMediaEntryArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$isAdmin = kCurrentContext::$is_admin_session;
		$newArr = new KalturaMediaEntryArray();
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
		parent::__construct("KalturaMediaEntry");	
	}
}