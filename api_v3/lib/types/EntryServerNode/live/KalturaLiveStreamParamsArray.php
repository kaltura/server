<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamParamsArray extends KalturaTypedArray {
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaLiveStreamParamsArray();
		if ($arr == null)
			return $newArr;
	
		foreach ($arr as $obj)
		{
			$nObj = new KalturaLiveStreamParams();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
	
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaLiveStreamParams");
	}
}