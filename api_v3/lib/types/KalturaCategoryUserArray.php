<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryUserArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaCategoryUserArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaCategoryUser();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaCategoryUser");
	}
}