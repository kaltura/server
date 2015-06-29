<?php
/**
 * @package plugins.integration
 */
interface IIntegrationProviderPlugin
{
	/**
	 * @return KalturaVersion
	 */
	public static function getRequiredIntegrationPluginVersion();
	
	/**
	 * Return class name that expand IntegrationProviderType enum
	 * @return string
	 */
	public static function getIntegrationProviderClassName();
}
