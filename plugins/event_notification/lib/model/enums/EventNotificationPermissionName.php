<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.enum
 */ 
class EventNotificationPermissionName implements IKalturaPluginEnum, PermissionName
{
	const SYSTEM_ADMIN_EVENT_NOTIFICATION_BASE = 'SYSTEM_ADMIN_EVENT_NOTIFICATION_BASE';
	const SYSTEM_ADMIN_EVENT_NOTIFICATION_MODIFY = 'SYSTEM_ADMIN_EVENT_NOTIFICATION_MODIFY';
	
	public static function getAdditionalValues()
	{
		return array
		(
			'SYSTEM_ADMIN_EVENT_NOTIFICATION_BASE' => self::SYSTEM_ADMIN_EVENT_NOTIFICATION_BASE,
			'SYSTEM_ADMIN_EVENT_NOTIFICATION_MODIFY' => self::SYSTEM_ADMIN_EVENT_NOTIFICATION_MODIFY,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
