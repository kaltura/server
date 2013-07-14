<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaGeoCoderType extends KalturaDynamicEnum implements geoCoderType
{
	public static function getEnumClass()
	{
		return 'geoCoderType';
	}
}