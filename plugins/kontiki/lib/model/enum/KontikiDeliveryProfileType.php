<?php

/**
 * @package plugins.velocix
 * @subpackage model.enum
 */ 
class KontikiDeliveryProfileType implements IKalturaPluginEnum, DeliveryProfileType
{
	const KONTIKI_HTTP = 'KONTIKI_HTTP';
	
	public static function getAdditionalValues()
	{
		return array
		(
			'KONTIKI_HTTP' => self::KONTIKI_HTTP,
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
