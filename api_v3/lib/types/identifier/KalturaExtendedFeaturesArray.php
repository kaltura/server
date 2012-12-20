<?php
/**
 * @package api
 * @subpackage enum
 * 
 */
class KalturaExtendedFeaturesArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaExtendedFeaturesArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaExtendedFeature();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaExtendedFeature");
	}
}