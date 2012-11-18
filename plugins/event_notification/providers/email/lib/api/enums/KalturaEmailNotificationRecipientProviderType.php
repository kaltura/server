<?php
class KalturaEmailNotificationRecipientProviderType extends DynamicEnum implements EmailNotificationRecipientProviderType 
{
	public static function getEnumClass()
	{
		return 'EmailNotificationRecipientProviderType';
	}
}