<?php
/**
 * Enable the plugin to add information to the entry investigation page
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaAdminConsoleEntryInvestigate extends IKalturaBase
{
	/**
	 * @return array<Kaltura_View_Helper_EntryInvestigatePlugin>
	 */
	public static function getEntryInvestigatePlugins();
}