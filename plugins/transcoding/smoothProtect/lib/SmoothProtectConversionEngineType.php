<?php
/**
 * @package plugins.smoothProtect
 * @subpackage lib
 */
class SmoothProtectConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const SMOOTHPROTECT = 'SmoothProtect';
	
	public static function getAdditionalValues()
	{
		return array(
			'SMOOTHPROTECT' => self::SMOOTHPROTECT
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
