<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaConvertCollectionFlavorDataArray extends KalturaTypedArray
{
	public static function fromConvertCollectionFlavorDataArray( $arr )
	{
		$newArr = new KalturaConvertCollectionFlavorDataArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaConvertCollectionFlavorData();
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaConvertCollectionFlavorData" );
	}
}
