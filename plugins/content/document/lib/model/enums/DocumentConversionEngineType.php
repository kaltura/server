<?php
/**
 * @package plugins.document
 * @subpackage lib.model.enums
 */
class DocumentConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{	
	const IMAGEMAGICK_ENGINE = 'ImageMagick';
	const PPT2IMG_ENGINE = 'ppt2Img';
	const THUMB_ASSETS_ENGINE = 'thumbAssets';

	public static function getAdditionalValues()
	{
		return array(
			'IMAGEMAGICK' => self::IMAGEMAGICK_ENGINE,
			'PPT2IMG' => self::PPT2IMG_ENGINE,
			'THUMB_ASSETS' => self::THUMB_ASSETS_ENGINE,
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