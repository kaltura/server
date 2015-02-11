<?php
/**
 * @package plugins.drm
 * @subpackage api.objects
 */
class KalturaDrmPolicyArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaDrmPolicyArray();
		foreach ( $arr as $obj )
		{
		    $nObj = KalturaDrmPolicy::getInstanceByType($obj->getProvider());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( 'KalturaDrmPolicy' );
	}
}
