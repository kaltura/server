<?php
/**
 * @package plugins.emailNotification
 * @subpackage api.enum
 * @see EmailNotificationFormat
 */
class KalturaEmailNotificationFormat extends KalturaDynamicEnum implements EmailNotificationFormat
{
	public static function getEnumClass()
	{
		return 'EmailNotificationFormat';
	}
}