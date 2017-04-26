<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaUserEntryExtendedStatus extends KalturaDynamicEnum implements UserEntryExtendedStatus
{
	public static function getEnumClass()
	{
		return 'UserEntryExtendedStatus';
	}
}