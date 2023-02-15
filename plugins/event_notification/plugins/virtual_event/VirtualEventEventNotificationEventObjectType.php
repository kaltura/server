<?php
/**
 * @package plugins.virtualEventEventNotifications
 * @subpackage lib
 */
class VirtualEventEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const VIRTUAL_EVENT = 'virtualEvent';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'VIRTUAL_EVENT' => self::VIRTUAL_EVENT,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			VirtualEventEventNotificationsPlugin::getApiValue(self::VIRTUAL_EVENT) => 'Virtual event object',
		);
	}	
}
