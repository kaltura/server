<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaCategoryArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaCategory();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaCategory");
	}
}
?>