<?php
/**
 * @package plugins.metadata
 * @subpackage api.enum
 */
class KalturaMetadataObjectType extends KalturaDynamicEnum implements MetadataObjectType
{
	public static function getEnumClass()
	{
		return 'MetadataObjectType';
	}
}