<?php
/**
 * Enum class for recipient provider types
 * 
 * @package plugins.emailNotification
 * @subpackage api.enums
 */
class KalturaEmailNotificationRecipientProviderType extends KalturaDynamicEnum implements EmailNotificationRecipientProviderType 
{
	public static function getEnumClass()
	{
		return 'EmailNotificationRecipientProviderType';
	}
}