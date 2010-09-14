<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserArray extends KalturaTypedArray
{
	public static function fromUserArray ( $arr )
	{
		$newArr = new KalturaUserArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaUser();
			$nObj->fromUser( $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaUser" );
	}
}
?>