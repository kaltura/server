<?php
/**
 * @package api
 * @subpackage enum
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
