<?php
/**
 * @package plugins.pushNotification
 * @subpackage api.objects
 */

class KalturaPushNotificationCommandType extends KalturaEnum implements PushNotificationCommandType
{
	public static function getEnumClass()
	{
		return 'PushNotificationCommandType';
	}

	public static function getAdditionalDescriptions()
	{
		return array(
				PushNotificationCommandType::CLEAR_QUEUE => 'Clear messgae queue.',
		);
	}
}