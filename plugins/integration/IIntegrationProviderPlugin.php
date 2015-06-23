<?php
/**
 * @package plugins.integration
 */
interface IIntegrationProviderPlugin
{
	/**
	 * @return KalturaVersion
	 */
	protected static function getRequiredIntegrationPluginVersion();
	
	/**
	 * Return class name that expand IntegrationProviderType enum
	 * @return string
	 */
	protected static function getIntegrationProviderClassName();
}
