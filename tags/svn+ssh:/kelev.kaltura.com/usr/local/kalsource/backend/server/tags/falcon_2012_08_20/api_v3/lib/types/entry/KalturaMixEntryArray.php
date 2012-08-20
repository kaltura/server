<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMixEntryArray extends KalturaTypedArray
{
	public static function fromEntryArray ( $arr )
	{
		$newArr = new KalturaMixEntryArray();
		if ($arr == null)
			return $newArr;		
		foreach ($arr as $obj)
		{
			$nObj = new KalturaMixEntry();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaMixEntry");	
	}
}