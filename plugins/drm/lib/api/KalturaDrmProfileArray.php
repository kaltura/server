<?php
/**
 * @package plugins.drm
 * @subpackage api.objects
 */
class KalturaDrmProfileArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaDrmProfileArray();
		foreach ( $arr as $obj )
		{
		    $nObj = KalturaDrmProfile::getInstanceByType($obj->getProvider());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( 'KalturaDrmProfile' );
	}
}
