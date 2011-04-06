<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderFileArray extends KalturaTypedArray
{
	public static function fromDbArray ( $arr )
	{
		$newArr = new KalturaDropFolderFileArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaDropFolderFile();
			$nObj->fromObject( $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( 'KalturaDropFolderFile' );
	}
}
