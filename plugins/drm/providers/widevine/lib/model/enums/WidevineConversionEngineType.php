<?php
/**
 * @package plugins.widevine
 * @subpackage model.enum
 */
class WidevineConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const WIDEVINE = 'Widevine';
	
	public static function getAdditionalValues()
	{
		return array(
			'WIDEVINE' => self::WIDEVINE,
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
