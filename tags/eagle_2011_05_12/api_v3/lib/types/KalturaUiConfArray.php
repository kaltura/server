<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUiConfArray extends KalturaTypedArray
{
	public static function fromUiConfArray ( $arr )
	{
		$newArr = new KalturaUiConfArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaUiConf();
			$nObj->fromUiConf( $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaUiConf" );
	}
}
?>