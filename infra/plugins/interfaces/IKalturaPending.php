<?php
/**
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaPending extends IKalturaBase
{
	/**
	 * Returns a Kaltura dependency object that defines the relationship between two plugins.
	 * 
	 * @return array<KalturaDependency> The Kaltura dependency object
	 */
	public static function dependsOn();
}