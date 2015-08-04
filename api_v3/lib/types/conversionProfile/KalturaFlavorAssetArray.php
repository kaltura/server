<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorAssetArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaFlavorAssetArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
		    $nObj = KalturaFlavorAsset::getInstance($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaFlavorAsset");	
	}
}