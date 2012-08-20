<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaPermissionName extends KalturaDynamicEnum implements PermissionName
{
	// see permissionName interface
	
	public static function getEnumClass()
	{
		return 'PermissionName';
	}
}