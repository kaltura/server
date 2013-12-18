<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFileAssetDescriptorArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaFileAssetDescriptorArray();
		if ($arr == null)
			return $newArr;
		foreach ($arr as $obj)
		{
    		$nObj = new KalturaFileAssetDescriptor();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaFileAssetDescriptor");	
	}
}
