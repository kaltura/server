<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaSourceType extends KalturaDynamicEnum implements EntrySourceType
{
	public static function getEnumClass()
	{
		return 'EntrySourceType';
	}
}
