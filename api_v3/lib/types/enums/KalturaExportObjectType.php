<?php

/**
 * @package api
 * @subpackage enum
 */
class KalturaExportObjectType extends KalturaDynamicEnum implements ExportObjectType
{
	public static function getEnumClass()
	{
		return 'ExportObjectType';
	}
}