<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaConvertCollectionFlavorDataArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaConvertCollectionFlavorDataArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaConvertCollectionFlavorData();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaConvertCollectionFlavorData" );
	}
}
