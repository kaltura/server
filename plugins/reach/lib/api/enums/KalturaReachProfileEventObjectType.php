<?php
/**
 * @package plugins.reach
 * @subpackage api.enum
 * @see EventNotificationEventType
 */
class KalturaReachProfileEventObjectType extends KalturaDynamicEnum implements EventNotificationEventObjectType
{
	public static function getEnumClass()
	{
		return 'EventNotificationEventType';
	}
}