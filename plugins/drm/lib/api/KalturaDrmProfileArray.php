<?php
/**
 * @package plugins.drm
 * @subpackage api.objects
 */
class KalturaDrmProfileArray extends KalturaTypedArray
{
	public static function fromDbArray ( $arr )
	{
		$newArr = new KalturaDrmProfileArray();
		foreach ( $arr as $obj )
		{
		    $nObj = KalturaDrmProfile::getInstanceByType($obj->getProvider());
			$nObj->fromObject( $obj );
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( 'KalturaDrmProfile' );
	}
}
