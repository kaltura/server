<?php
/**
 * An array of KalturaAssetTypeInfo
 * 
 * @package api
 * @subpackage objects
 */
class KalturaAssetTypeInfoArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaAssetTypeInfoArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = new KalturaAssetTypeInfo();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct()
	{
		return parent::__construct("KalturaAssetTypeInfo");
	}
}

