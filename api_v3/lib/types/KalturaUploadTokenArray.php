<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUploadTokenArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUploadTokenArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaUploadToken();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaUploadToken");
	}
}
