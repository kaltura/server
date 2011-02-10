<?php
/**
 * @package plugins.avidemux
 * @subpackage batch.conversion
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
