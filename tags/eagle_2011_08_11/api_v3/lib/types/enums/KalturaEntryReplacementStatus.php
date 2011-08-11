<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaEntryReplacementStatus extends KalturaDynamicEnum implements entryReplacementStatus
{
	public static function getEnumClass()
	{
		return 'entryReplacementStatus';
	}
}