<?php

/**
 * @package plugins.velocix
 * @subpackage model.enum
 */ 
class VelocixDeliveryProfileType implements IKalturaPluginEnum, DeliveryProfileType
{
	const VELOCIX_HDS = 'VELOCIX_HDS';
	const VELOCIX_HLS = 'VELOCIX_HLS';
	
	public static function getAdditionalValues()
	{
		return array
		(
			'VELOCIX_HDS' => self::VELOCIX_HDS,
			'VELOCIX_HLS' => self::VELOCIX_HLS,
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
