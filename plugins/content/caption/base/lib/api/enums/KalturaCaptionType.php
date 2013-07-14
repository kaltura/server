<?php
/**
 * @package plugins.caption
 * @subpackage api.enum
 */
class KalturaCaptionType extends KalturaDynamicEnum implements CaptionType
{
	public static function getEnumClass()
	{
		return 'CaptionType';
	}
}