<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.enum
 * @see EventNotificationEventObjectType
 */
class KalturaEventNotificationEventObjectType extends KalturaDynamicEnum implements EventNotificationEventObjectType
{
	public static function getEnumClass()
	{
		return 'EventNotificationEventObjectType';
	}
}