<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage api.objects
 */

class KalturaWebexAPIIntegrationSettingArray extends KalturaTypedArray
{
	public static function fromDbArray(array $arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaWebexAPIIntegrationSettingArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaWebexAPIIntegrationSetting();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	public function __construct( )
	{
		return parent::__construct ( 'KalturaWebexAPIIntegrationSetting' );
	}
}