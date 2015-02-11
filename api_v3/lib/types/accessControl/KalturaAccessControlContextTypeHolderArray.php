<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated use KalturaContextTypeHolderArray
 */
class KalturaAccessControlContextTypeHolderArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaAccessControlContextTypeHolderArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $type)
		{
    		$nObj = new KalturaAccessControlContextTypeHolder();
			$nObj->type = $type;
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		parent::__construct("KalturaAccessControlContextTypeHolder");	
	}
}