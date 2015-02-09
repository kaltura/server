<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaExtendingItemMrssParameterArray extends KalturaTypedArray
{
	
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaExtendingItemMrssParameterArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaExtendingItemMrssParameter();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaExtendingItemMrssParameter");
	}
}