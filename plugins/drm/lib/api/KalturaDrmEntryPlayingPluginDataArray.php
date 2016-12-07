<?php
/**
 * @package plugins.drm
 * @subpackage api.objects
 */
class KalturaDrmEntryPlayingPluginDataArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaDrmEntryPlayingPluginDataArray();
		foreach ( $arr as $obj )
		{
			$nObj = KalturaPluginManager::loadObject('KalturaDrmEntryPlayingPluginData', get_class($obj));
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
		 
	}
	
	public function __construct( )
	{
		return parent::__construct ( 'KalturaDrmEntryPlayingPluginData' );
	}
}
