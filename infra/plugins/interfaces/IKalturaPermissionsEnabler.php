<?php
/**
 * Enable the plugin to perform actions on permission enablemenet
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaPermissionsEnabler extends IKalturaPermissions
{
	/**
	 * Grants or denies a partner permission to use a plugin.
	 * 
	 * @param int $partnerId The ID of the partner the permission is enabled for
	 * @param string Enabled permission name.
	 */
	public static function permissionEnabled($partnerId, $permissionName);
	
}