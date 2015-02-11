<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorAssetArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaFlavorAssetArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = KalturaFlavorAsset::getInstanceByType($obj->getType());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaFlavorAsset");	
	}
}