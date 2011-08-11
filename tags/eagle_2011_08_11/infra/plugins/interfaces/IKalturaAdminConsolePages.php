<?php
/**
 * Enable to plugin to add pages to the admin console
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaAdminConsolePages extends IKalturaBase
{
	/**
	 * @return array
	 */
	public static function getAdminConsolePages();	
}