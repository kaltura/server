<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaEntryServerNodeStatus extends KalturaDynamicEnum implements EntryServerNodeStatus{

	public static function getEnumClass()
	{
		return 'EntryServerNodeStatus';
	}
}