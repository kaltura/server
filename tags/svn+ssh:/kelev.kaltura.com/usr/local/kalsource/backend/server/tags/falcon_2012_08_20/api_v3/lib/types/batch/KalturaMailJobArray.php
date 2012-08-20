<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaMailJobArray extends KalturaTypedArray
{
	public static function fromMailJobArray ( $arr )
	{
		$newArr = new KalturaMailJobArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaMailJob();
			$nObj->fromMailJob( $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaMailJob" );
	}
}
?>