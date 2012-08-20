<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaConversionProfileAssetParamsArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaConversionProfileAssetParamsArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaConversionProfileAssetParams();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaConversionProfileAssetParams");	
	}
}