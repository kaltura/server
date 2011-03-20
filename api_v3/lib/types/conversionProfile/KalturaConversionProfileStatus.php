<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaConversionProfileStatus extends KalturaDynamicEnum implements ConversionProfileStatus
{
	public static function getEnumClass()
	{
		return 'ConversionProfileStatus';
	}
}
