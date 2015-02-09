<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUiConfArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUiConfArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaUiConf();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaUiConf" );
	}
}
