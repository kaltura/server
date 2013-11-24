<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaFileAssetStatus extends KalturaDynamicEnum implements FileAssetStatus
{
	public static function getEnumClass()
	{
		return 'FileAssetStatus';
	}
}