<?php
/**
 * @package plugins.drm
 * @subpackage api.objects
 */
class KalturaDrmPlaybackPluginDataArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaDrmPlaybackPluginDataArray();
		foreach ( $arr as $obj )
		{
			$nObj = KalturaPluginManager::loadObject('KalturaDrmPlaybackPluginData', get_class($obj));
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( 'KalturaDrmPlaybackPluginData' );
	}
}
