<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaUserEntryStatus extends KalturaDynamicEnum implements UserEntryStatus
{
	public static function getEnumClass()
	{
		return 'UserEntryStatus';
	}
}

