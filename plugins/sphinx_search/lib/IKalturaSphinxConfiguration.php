<?php
/**
 * Enable the plugin to add sphinx indexes
 * @package plugins.sphinxSearch
 * @subpackage lib
 */
interface IKalturaSphinxConfiguration extends IKalturaBase
{
	/**
	 * @return string path to configuration file
	 */
	public static function getSphinxConfigPath();
}