<?php

/**
 * @package api
 * @subpackage enum
 */
class KalturaExportToCsvOptionsType extends KalturaDynamicEnum implements ExportToCsvOptionsType
{
	public static function getEnumClass()
	{
		return 'ExportToCsvOptions';
	}
}
