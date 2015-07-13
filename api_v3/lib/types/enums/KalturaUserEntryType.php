<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaUserEntryType extends KalturaDynamicEnum implements UserEntryType
{
	public static function getEnumClass()
	{
		return 'UserEntryType';
	}
}