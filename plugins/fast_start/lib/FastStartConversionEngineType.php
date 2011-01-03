<?php
/**
 * @package api
 * @subpackage enum
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
