<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaEntryApplication extends KalturaDynamicEnum implements EntryApplication
{
	public static function getEnumClass()
	{
		return 'EntryApplication';
	}
}
