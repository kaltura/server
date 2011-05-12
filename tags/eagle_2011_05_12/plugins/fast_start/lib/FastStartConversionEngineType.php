<?php
/**
 * @package plugins.fastStart
 * @subpackage lib
 */
class FastStartConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const FAST_START = 'FastStart';
	
	public static function getAdditionalValues()
	{
		return array(
			'FAST_START' => self::FAST_START
		);
	}
}
