<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMixEntryArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaMixEntryArray();
		if ($arr == null)
			return $newArr;		
		foreach ($arr as $obj)
		{
    		$nObj = KalturaEntryFactory::getInstanceByType($obj->getType());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaMixEntry");	
	}
}