<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaUserEntryType extends KalturaDynamicEnum implements userEntryType
{
	public static function getEnumClass()
	{
		return 'UserEntryType';
	}
}