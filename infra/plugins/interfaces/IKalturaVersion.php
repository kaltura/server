<?php
/**
 * Enable you to give version to the plugin
 * The version might be importent for depencies between different plugins
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaVersion extends IKalturaBase
{
	/**
	 * @return KalturaVersion
	 */
	public static function getVersion();
}