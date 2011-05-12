<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaWidgetArray extends KalturaTypedArray
{
	public static function fromWidgetArray ( $arr )
	{
		$newArr = new KalturaWidgetArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaWidget();
			$nObj->fromWidget( $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaWidget" );
	}
}
?>