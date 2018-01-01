<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaConversionEngineType extends KalturaDynamicEnum implements conversionEngineType, dataExtractEngineType
{
	public static function getEnumClass()
	{
		return 'conversionEngineType';
	}
}
