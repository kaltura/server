<?php
/**
 * @package api
 * @subpackage enum
 */
class SegmenterConversionEngineType implements IKalturaPluginEnum, conversionEngineType
{
	const SEGMENTER = 'Segmenter';
	
	public static function getAdditionalValues()
	{
		return array(
			'SEGMENTER' => self::SEGMENTER
		);
	}
}
