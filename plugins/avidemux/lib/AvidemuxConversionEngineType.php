<?php
/**
 * @package plugins.avidemux
 * @subpackage lib
 */
class AvidemuxConversionEngineType implements conversionEngineType, IKalturaPluginEnum
{
	const AVIDEMUX = 'Avidemux';
	
	public static function getAdditionalValues()
	{
		return array(
			'AVIDEMUX' => self::AVIDEMUX
		);
	}
}
