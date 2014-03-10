<?php
/**
 * @package api
 * @subpackage objects.factory
 */
class KalturaEntryFactory
{
	/**
	 * @param int $type
	 * @param bool $isAdmin
	 * @return KalturaBaseEntry
	 */
	static function getInstanceByType ($type, $isAdmin = false)
	{
		switch ($type) 
		{
			case KalturaEntryType::MEDIA_CLIP:
				$obj = new KalturaMediaEntry();
				break;
				
			case KalturaEntryType::MIX:
				$obj = new KalturaMixEntry();
				break;
				
			case KalturaEntryType::PLAYLIST:
				$obj = new KalturaPlaylist();
				break;
				
			case KalturaEntryType::DATA:
				$obj = new KalturaDataEntry();
				break;
				
			case KalturaEntryType::LIVE_STREAM:
				if($isAdmin)
				{
					$obj = new KalturaLiveStreamAdminEntry();
				}
				else
				{
					$obj = new KalturaLiveStreamEntry();
				}
				break;
				
			case KalturaEntryType::LIVE_CHANNEL:
				$obj = new KalturaLiveChannel();
				break;
				
			default:
				$obj = KalturaPluginManager::loadObject('KalturaBaseEntry', $type);
				
				if(!$obj)
					$obj = new KalturaBaseEntry();
					
				break;
		}
		
		return $obj;
	}
}
