<?php
/**
 * @package plugins.document
 * @subpackage lib.model.enums
 */
class DocumentConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{	
	const IMAGEMAGICK_ENGINE = 'ImageMagick';
	const PPT2IMG_ENGINE = 'ppt2Img';
	
	public static function getAdditionalValues()
	{
		return array(
			'IMAGEMAGICK' => self::IMAGEMAGICK_ENGINE,
			'PPT2IMG' => self::PPT2IMG_ENGINE,
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