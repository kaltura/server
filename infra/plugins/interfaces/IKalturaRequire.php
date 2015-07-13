<?php
/**
 * Enable the plugin to define another plugin as a mandatory requirement for its load
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaRequire extends IKalturaBase
{
	/**
	 * Returns string(s) of Kaltura Plugins which the plugin requires
	 * 
	 * @return array<String> The Kaltura dependency object
	 */
	public static function requires();
}