<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaEntryStatus extends KalturaDynamicEnum implements entryStatus
{
	public static function getEnumClass()
	{
		return 'entryStatus';
	}
}