<?php
/**
 * @package plugins.caption
 * @subpackage api.enum
 */
class KalturaCaptionSource extends KalturaDynamicEnum implements CaptionSource
{
	public static function getEnumClass()
	{
		return 'CaptionSource';
	}
}