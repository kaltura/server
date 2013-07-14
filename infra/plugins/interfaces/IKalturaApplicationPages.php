<?php
/**
 * Enable to plugin to add pages to external applications
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaApplicationPages extends IKalturaBase
{
	/**
	 * @return array
	 */
	public static function getApplicationPages();	
}