<?php
/**
 * @package plugins.pushNotification
 * @subpackage api.objects
 */

class KalturaPushNotificationCommandType extends KalturaStringEnum implements PushNotificationCommandType
{
	public static function getEnumClass()
	{
		return 'PushNotificationCommandType';
	}

	public static function getAdditionalDescriptions()
	{
		return array(
				PushNotificationCommandType::CLEAR_QUEUE => 'Clear message queue.',
		);
	}
}