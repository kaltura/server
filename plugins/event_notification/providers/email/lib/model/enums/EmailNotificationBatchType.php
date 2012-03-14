<?php

/**
 * @package plugins.emailNotification
 * @subpackage model.enum
 */ 
class EmailNotificationBatchType implements IKalturaPluginEnum, BatchJobType
{
	const EMAIL_NOTIFICATION_HANDLER = 'EmailNotificationHandler';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'EMAIL_NOTIFICATION_HANDLER' => self::EMAIL_NOTIFICATION_HANDLER,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() 
	{
		return array();
	}

}
