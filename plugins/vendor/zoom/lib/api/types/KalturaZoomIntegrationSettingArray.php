<?php
/**
 * @package plugins.vendor
 * @subpackage api.objects
 */

class KalturaZoomIntegrationSettingArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaZoomIntegrationSettingArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaZoomIntegrationSetting();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaZoomIntegrationSetting" );
	}
}