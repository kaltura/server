<?php
/**
 * @package api
 * @subpackage api.enum
 */
class KalturaDrmSchemeName extends KalturaDynamicEnum implements DrmSchemeName
{
	public static function getEnumClass()
	{
		return 'DrmSchemeName';
	}
}