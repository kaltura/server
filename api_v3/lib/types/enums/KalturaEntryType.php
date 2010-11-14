<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaEntryType extends KalturaDynamicEnum implements entryType
{
	public static function getEnumClass()
	{
		return 'entryType';
	}
}
