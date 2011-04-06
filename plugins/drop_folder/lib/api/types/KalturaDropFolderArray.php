<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderArray extends KalturaTypedArray
{
	public static function fromDbArray ( $arr )
	{
		$newArr = new KalturaDropFolderArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaDropFolder();
			$nObj->fromObject( $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( 'KalturaDropFolder' );
	}
}
