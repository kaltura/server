<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFlavorAssetWithParamsArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaFlavorAssetWithParamsArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaFlavorAssetWithParams();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaFlavorAssetWithParams");	
	}
}