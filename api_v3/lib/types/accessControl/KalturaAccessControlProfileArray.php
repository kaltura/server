<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAccessControlProfileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaAccessControlProfileArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
    		$nObj = new KalturaAccessControlProfile();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaAccessControlProfile");	
	}
}