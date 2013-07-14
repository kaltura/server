<?php
/**
 * @package plugins.emailNotification
 * @subpackage model.enum
 */
class EmailNotificationTemplateType implements IKalturaPluginEnum, EventNotificationTemplateType
{
	const EMAIL = 'Email';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'EMAIL' => self::EMAIL,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() 
	{
		return array(
			self::EMAIL => 'Email event notification',
		);
	}
}
