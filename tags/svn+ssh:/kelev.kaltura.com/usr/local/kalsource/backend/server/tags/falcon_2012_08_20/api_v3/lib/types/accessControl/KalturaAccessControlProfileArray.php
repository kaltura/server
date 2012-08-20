<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlProfileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaAccessControlProfileArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaAccessControlProfile();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaAccessControlProfile");	
	}
}