<?php
/**
 * Abstract core class  which provides the recipients (to, CC, BCC) for an email notification
 * @package plugins.emailNotification
 * @subpackage model.data
 */
abstract class KalturaEmailNotificationRecipientProvider extends KalturaObject
{
	public static function getProviderInstance ($dbObject)
	{
		switch (get_class($dbObject))
		{
			case 'kEmailNotificationStaticRecipientProvider':
				$instance = new KalturaEmailNotificationStaticRecipientProvider();
				break;
			case 'kEmailNotificationCategoryRecipientProvider':
				$instance = new KalturaEmailNotificationCategoryRecipientProvider();
				break;
			case 'kEmailNotificationUserRecipientProvider':
				$instance = new KalturaEmailNotificationUserRecipientProvider();
				break;
			case 'kEmailNotificationGroupRecipientProvider':
				$instance = new KalturaEmailNotificationGroupRecipientProvider();
				break;
			default:
				$instance = KalturaPluginManager::loadObject('kEmailNotificationRecipientProvider', get_class($dbObject));
				break;
		}
		
		if ($instance)
			$instance->fromObject($dbObject);
		
		return $instance;
	}
}