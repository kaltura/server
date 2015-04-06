<?php
/**
 * @package plugins.externalMedia
 * @subpackage api.objects
 */
class KalturaExternalMediaEntryArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaExternalMediaEntryArray();
		if($arr == null)
			return $newArr;
		
		foreach($arr as $obj)
		{
    		$nObj = KalturaEntryFactory::getInstanceByType($obj->getType());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaExternalMediaEntry");	
	}
}