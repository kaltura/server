<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaStorageProfileProtocol extends KalturaDynamicEnum implements StorageProfileProtocol
{
	public static function getEnumClass()
	{
		return 'StorageProfileProtocol';
	}
}
