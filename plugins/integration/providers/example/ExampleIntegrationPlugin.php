<?php
/**
 * @package plugins.exampleIntegration
 */
class ExampleIntegrationPlugin extends IntegrationProviderPlugin
{
	const PLUGIN_NAME = 'exampleIntegration';
	const INTEGRATION_PLUGIN_VERSION_MAJOR = 1;
	const INTEGRATION_PLUGIN_VERSION_MINOR = 0;
	const INTEGRATION_PLUGIN_VERSION_BUILD = 0;

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IntegrationProviderPlugin::getRequiredIntegrationPluginVersion()
	 */
	public static function getRequiredIntegrationPluginVersion()
	{
		return new KalturaVersion(
			self::INTEGRATION_PLUGIN_VERSION_MAJOR,
			self::INTEGRATION_PLUGIN_VERSION_MINOR,
			self::INTEGRATION_PLUGIN_VERSION_BUILD
		);
	}
	
	/* (non-PHPdoc)
	 * @see IntegrationProviderPlugin::getIntegrationProviderClassName()
	 */
	public static function getIntegrationProviderClassName()
	{
		return 'ExampleIntegrationProviderType';
	}
	
	/*
	 * @return IIntegrationProvider
	 */
	public function getProvider()
	{
		return new IntegrationExampleProvider();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'kIntegrationJobProviderData' && $enumValue == self::getApiValue(ExampleIntegrationProviderType::EXAMPLE))
		{
			return 'kExampleIntegrationJobProviderData';
		}
	
		if($baseClass == 'KalturaIntegrationJobProviderData')
		{
			if($enumValue == self::getApiValue(ExampleIntegrationProviderType::EXAMPLE) || $enumValue == self::getIntegrationProviderCoreValue(ExampleIntegrationProviderType::EXAMPLE))
				return 'KalturaExampleIntegrationJobProviderData';
		}
	
		if($baseClass == 'KIntegrationEngine' || $baseClass == 'KIntegrationCloserEngine')
		{
			if($enumValue == KalturaIntegrationProviderType::EXAMPLE)
				return 'KExampleIntegrationEngine';
		}
		if($baseClass == 'IIntegrationProvider' && $enumValue == self::getIntegrationProviderCoreValue(ExampleIntegrationProviderType::EXAMPLE))
		{
			return 'IntegrationExampleProvider';
		}
	}
}
