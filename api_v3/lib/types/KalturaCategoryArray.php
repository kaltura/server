<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryArray extends KalturaTypedArray
{
	public static function fromCategoryArray($arr)
	{
		$newArr = new KalturaCategoryArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaCategory();
			$nObj->fromObject($obj);
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