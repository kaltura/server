<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadResultArray extends KalturaTypedArray
{
	public static function fromBulkUploadResultArray( $arr )
	{
		$newArr = new KalturaBulkUploadResultArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaBulkUploadResult();
			$nObj->fromObject( $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaBulkUploadResult" );
	}
}
?>