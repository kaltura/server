<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaEntryServerNodeType extends KalturaDynamicEnum implements EntryServerNodeType
{
	public static function getEnumClass()
	{
		return 'EntryServerNodeType';
	}
}