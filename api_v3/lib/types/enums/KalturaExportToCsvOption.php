<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaExportToCsvOption extends KalturaDynamicEnum implements ExportToCsvOption
{
	public static function getEnumClass()
	{
		return 'ExportToCsvOption';
	}
}
