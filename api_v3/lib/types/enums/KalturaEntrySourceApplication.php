<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaEntrySourceApplication extends KalturaDynamicEnum implements EntrySourceApplication
{
	public static function getEnumClass()
	{
		return 'EntrySourceApplication';
	}
}