<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUploadTokenArray extends KalturaTypedArray
{
	public static function fromUploadTokenArray($arr)
	{
		$newArr = new KalturaUploadTokenArray();
		foreach($arr as $obj)
		{
			$nObj = new KalturaUploadToken();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaUploadToken");
	}
}
?>