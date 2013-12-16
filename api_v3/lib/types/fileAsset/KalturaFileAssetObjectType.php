<?php
/**
 * @package api
 * @subpackage filters.enum
 */
class KalturaFileAssetObjectType extends KalturaDynamicEnum implements FileAssetObjectType
{
	public static function getEnumClass()
	{
		return 'FileAssetObjectType';
	}
}