<?php
/**
 * @package plugins.document
 * @subpackage lib.model.enums
 */
class DocumentConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{	
	const IMAGEMAGICK_ENGINE = 'ImageMagick';
	
	public static function getAdditionalValues()
	{
		return array(
			'IMAGEMAGICK' => self::IMAGEMAGICK_ENGINE,
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