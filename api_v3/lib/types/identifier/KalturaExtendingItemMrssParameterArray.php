<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaExtendingItemMrssParameterArray extends KalturaTypedArray
{
	
	public static function fromExtendingItemMrssParameterArray($arr)
	{
		$newArr = new KalturaExtendingItemMrssParameterArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaExtendingItemMrssParameter();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaExtendingItemMrssParameter");
	}
}