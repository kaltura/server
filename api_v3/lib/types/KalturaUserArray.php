<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUserArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaUserArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaUser();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaUser" );
	}
}
