<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.enum
 * @see EventNotificationEventType
 */
class KalturaEventNotificationEventType extends KalturaDynamicEnum implements EventNotificationEventType
{
	public static function getEnumClass()
	{
		return 'EventNotificationEventType';
	}
}