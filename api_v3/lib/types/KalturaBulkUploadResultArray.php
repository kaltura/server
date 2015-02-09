<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadResultArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaBulkUploadResultArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaBulkUploadResult();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaBulkUploadResult" );
	}
}
