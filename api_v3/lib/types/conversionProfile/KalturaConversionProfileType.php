<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaConversionProfileType extends KalturaDynamicEnum implements ConversionProfileType
{
	public static function getEnumClass()
	{
		return 'ConversionProfileType';
	}
}
