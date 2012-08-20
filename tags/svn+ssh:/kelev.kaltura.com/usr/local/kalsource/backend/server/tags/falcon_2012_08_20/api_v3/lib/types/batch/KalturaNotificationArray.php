<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaNotificationArray extends KalturaTypedArray
{
	public static function fromNotificationArray ( $arr )
	{
		$newArr = new KalturaNotificationArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaNotification();
			$nObj->fromNotification( $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaNotification" );
	}
}
?>