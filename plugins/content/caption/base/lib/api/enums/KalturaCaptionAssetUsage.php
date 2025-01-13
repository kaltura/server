<?php

/**
 * @package plugins.caption
 * @subpackage model.enum
 */
class KalturaCaptionAssetUsage extends KalturaDynamicEnum implements CaptionUsage
{
	public static function getEnumClass()
	{
		return 'CaptionUsage';
	}
}
