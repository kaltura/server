<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaEntryCapability extends KalturaDynamicEnum implements EntryCapability
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'EntryCapability';
	}
}
