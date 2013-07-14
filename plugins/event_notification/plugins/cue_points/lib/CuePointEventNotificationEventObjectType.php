<?php
/**
 * @package plugins.cuePointEventNotifications
 * @subpackage lib
 */
class CuePointEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const CUE_POINT = 'CuePoint';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'CUE_POINT' => self::CUE_POINT,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			CuePointEventNotificationsPlugin::getApiValue(self::CUE_POINT) => 'Any cue point object or extension of cue point',
		);
	}
}
