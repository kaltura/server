<?php
/**
 * @package plugins.adCuePoint
 * @subpackage api.enum
 */
class KalturaAdType extends KalturaDynamicEnum implements AdType
{
	public static function getEnumClass()
	{
		return 'AdType';
	}
}