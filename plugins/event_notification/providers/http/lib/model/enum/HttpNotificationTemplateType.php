<?php
/**
 * @package plugins.httpNotification
 * @subpackage model.enum
 */
class HttpNotificationTemplateType implements IKalturaPluginEnum, EventNotificationTemplateType
{
	const HTTP = 'Http';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'HTTP' => self::HTTP,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() 
	{
		return array(
			self::HTTP => 'Http event notification',
		);
	}
}
