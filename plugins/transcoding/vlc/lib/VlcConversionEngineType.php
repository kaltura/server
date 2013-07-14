<?php
/**
 * @package plugins.vlc
 * @subpackage lib
 */
class VlcConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const VLC = 'Vlc';
	
	public static function getAdditionalValues()
	{
		return array(
			'VLC' => self::VLC
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
