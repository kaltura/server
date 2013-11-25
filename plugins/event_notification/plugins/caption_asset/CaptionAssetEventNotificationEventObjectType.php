<?php
/**
 * @package plugins.captionAssetEventNotifications
 * @subpackage lib
 */
class CaptionAssetEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const CAPTION_ASSET = 'CaptionAsset';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'CAPTION_ASSET' => self::CAPTION_ASSET,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			CaptionAssetEventNotificationsPlugin::getApiValue(self::CAPTION_ASSET) => 'Caption asset object',
		);
	}	
}
