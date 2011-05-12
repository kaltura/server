<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSearchResultArray extends KalturaTypedArray
{
	public static function fromSearchResultArray ( $arr , KalturaSearch $search )
	{
		$newArr = new KalturaSearchResultArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaSearchResult();
			$nObj->fromSearchResult( $obj , $search );
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaSearchResult" );
	}
}
?>