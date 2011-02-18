<?php
/**
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaPermissions extends IKalturaBase
{
	/**
	 * Grants or denies a partner permission to use a plugin.
	 * 
	 * @param int $partnerId The ID of the partner being checked for permission
	 * @return bool The partner is allowed to use the plugin or not.
	 */
	public static function isAllowedPartner($partnerId);
	
}