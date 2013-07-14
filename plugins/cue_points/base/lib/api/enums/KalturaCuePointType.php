<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.enum
 */
class KalturaCuePointType extends KalturaDynamicEnum implements CuePointType
{
	public static function getEnumClass()
	{
		return 'CuePointType';
	}
}