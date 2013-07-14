<?php
/**
 * Enable to plugin to add translated keys
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaApplicationTranslations extends IKalturaBase
{
	/**
	 * @return array
	 */
	public static function getTranslations($locale);	
}