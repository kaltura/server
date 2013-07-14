<?php
/**
 * Enable the plugin to add partner actions in the admin console partners management page
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaAdminConsolePublisherAction extends IKalturaBase
{
	/**
	 * @return array<string, string> - array of <label, jsActionFunctionName> 
	 */
	public function getPublisherAdminActionOptions($partner, $permissions);
	
	/**
	 * @return string javascript code to add to publisher list view
	 */
	public function getPublisherAdminActionJavascript();
}