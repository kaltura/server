<?php
/**
 * Plugin to append configuration
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaConfigurator extends IKalturaBase
{
	/**
	 * Merge configuration data from the plugin
	 * 
	 * @param string $configName
	 * @return Iterator
	 */
	public static function getConfig($configName);
}