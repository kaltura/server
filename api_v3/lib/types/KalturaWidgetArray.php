<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaWidgetArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaWidgetArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaWidget();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaWidget" );
	}
}
