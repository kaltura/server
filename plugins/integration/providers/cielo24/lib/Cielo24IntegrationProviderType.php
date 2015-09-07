<?php
/**
 * @package plugins.cielo24
 * @subpackage lib.enum
 */
class Cielo24IntegrationProviderType implements IKalturaPluginEnum, IntegrationProviderType
{
	const CIELO24 = 'Cielo24';
	
	public static function getAdditionalValues()
	{
		return array(
			'CIELO24' => self::CIELO24,
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
