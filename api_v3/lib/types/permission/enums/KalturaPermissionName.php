<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaPermissionName extends KalturaDynamicEnum implements permissionName
{
	// see permissionName interface
	
	public static function getEnumClass()
	{
		return 'permissionName';
	}
}