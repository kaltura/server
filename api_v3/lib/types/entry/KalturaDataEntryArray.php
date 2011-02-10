<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaDataEntryArray extends KalturaTypedArray
{
	public static function fromEntryArray ( $arr )
	{
		$newArr = new KalturaDataEntryArray();
		if ($arr == null)
			return $newArr;		
		foreach ($arr as $obj)
		{
			$nObj = new KalturaDataEntry();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaDataEntry");	
	}
}