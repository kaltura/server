<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaAssetType extends KalturaDynamicEnum implements assetType
{
	public static function getEnumClass()
	{
		return 'assetType';
	}
}
