<?php
/**
 * @package plugins.adCuePointEventNotifications
 * @subpackage lib
 */
class AdCuePointEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const AD_CUE_POINT = 'AdCuePoint';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'AD_CUE_POINT' => self::AD_CUE_POINT,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			AdCuePointEventNotificationsPlugin::getApiValue(self::AD_CUE_POINT) => 'Ad cue point object',
		);
	}
}
