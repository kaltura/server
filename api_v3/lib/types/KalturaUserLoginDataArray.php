<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserLoginDataArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUserLoginDataArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaUserLoginData();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaUserLoginData" );
	}
}
