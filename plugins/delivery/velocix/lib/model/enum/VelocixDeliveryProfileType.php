<?php

/**
 * @package plugins.velocix
 * @subpackage model.enum
 */ 
class VelocixDeliveryProfileType implements IKalturaPluginEnum, DeliveryProfileType
{
	const VELOCIX = 'Velocix';
	
	public static function getAdditionalValues()
	{
		return array
		(
			'VELOCIX' => self::VELOCIX,
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
