<?php
/**
 * @package plugins.uplynk
 * @subpackage model.enum
 */
class WowzaDeliveryProfileType implements IKalturaPluginEnum, DeliveryProfileType
{
	const WOWZA_HDS = 'WOWZA_HDS';
	const WOWZA_HLS = 'WOWZA_HLS';
	const WOWZA_APPLE_HTTP = 'WOWZA_APPLE_HTTP';
	
	public static function getAdditionalValues()
	{
		return array(
			'WOWZA_HDS' => self::WOWZA_HDS,
			'WOWZA_HLS' => self::WOWZA_HLS,
			'WOWZA_APPLE_HTTP' => self::WOWZA_APPLE_HTTP,
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
