<?php
/**
 * @package plugins.scheduleEventNotifications
 * @subpackage lib
 */
class ScheduleEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const SCHEDULE_EVENT = 'ScheduleEvent';
	const SCHEDULE_RESOURCE = 'ScheduleResource';
	const SCHEDULE_EVENT_RESOURCE = 'ScheduleEventResource';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'SCHEDULE_EVENT' => self::SCHEDULE_EVENT,
			'SCHEDULE_RESOURCE' => self::SCHEDULE_RESOURCE,
			'SCHEDULE_EVENT_RESOURCE' => self::SCHEDULE_EVENT_RESOURCE,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
		);
	}
}
