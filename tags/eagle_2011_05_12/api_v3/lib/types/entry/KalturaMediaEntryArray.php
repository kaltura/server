<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMediaEntryArray extends KalturaTypedArray
{
	public static function fromEntryArray ( $arr )
	{
		$newArr = new KalturaMediaEntryArray();
		if ($arr == null)
			return $newArr;		
		foreach ($arr as $obj)
		{
			$nObj = new KalturaMediaEntry();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaMediaEntry");	
	}
}