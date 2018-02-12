<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCsvAdditionalFieldInfoArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaCsvAdditionalFieldInfoArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaCsvAdditionalFieldInfo();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct()
	{
		return parent::__construct("KalturaCsvAdditionalFieldInfo");
	}
}