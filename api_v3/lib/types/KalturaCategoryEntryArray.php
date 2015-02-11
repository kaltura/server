<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryEntryArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaCategoryEntryArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaCategoryEntry();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaCategoryEntry");
	}
}