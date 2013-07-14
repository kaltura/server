<?php
/**
 * @package plugins.mp4box
 * @subpackage lib
 */
class Mp4boxConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const MP4BOX = 'Mp4box';
	
	public static function getAdditionalValues()
	{
		return array(
			'MP4BOX' => self::MP4BOX
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
