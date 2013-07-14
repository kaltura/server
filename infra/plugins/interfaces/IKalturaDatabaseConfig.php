<?php
/**
 * Enable you to add database connections
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaDatabaseConfig extends IKalturaBase
{
	/**
	 * @return array
	 */
	public static function getDatabaseConfig();	
}