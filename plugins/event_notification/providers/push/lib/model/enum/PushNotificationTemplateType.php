<?php
/**
 * @package plugins.pushNotification
 * @subpackage model.enum
 */
class PushNotificationTemplateType implements IKalturaPluginEnum, EventNotificationTemplateType
{
	const PUSH = 'Push';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'PUSH' => self::PUSH,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() 
	{
		return array(
			self::PUSH => 'Push event notification',
		);
	}
}
