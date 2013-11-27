<?php
/**
 * @package plugins.scheduledTaskEventNotification
 * @subpackage model.enum
 */
class DispatchEventNotificationObjectTaskType implements IKalturaPluginEnum, ObjectTaskType
{
	const DISPATCH_EVENT_NOTIFICATION = 'DispatchEventNotification';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'DISPATCH_EVENT_NOTIFICATION' => self::DISPATCH_EVENT_NOTIFICATION,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			ScheduledTaskEventNotificationPlugin::getApiValue(self::DISPATCH_EVENT_NOTIFICATION) => 'Dispatch event notification',
		);
	}
}
