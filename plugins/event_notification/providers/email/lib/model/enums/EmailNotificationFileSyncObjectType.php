<?php
/**
 * @package plugins.emailNotification
 * @subpackage model.enum
 */
class EmailNotificationFileSyncObjectType implements IKalturaPluginEnum, FileSyncObjectType
{
	const EMAIL_NOTIFICATION_TEMPLATE = 'EmailNotificationTemplate';
	
	public static function getAdditionalValues()
	{
		return array(
			'EMAIL_NOTIFICATION_TEMPLATE' => self::EMAIL_NOTIFICATION_TEMPLATE,
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
