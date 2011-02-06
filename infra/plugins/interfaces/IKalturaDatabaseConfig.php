<?php
/**
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