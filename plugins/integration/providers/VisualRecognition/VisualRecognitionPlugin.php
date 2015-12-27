<?php
/**
 * @package plugins.visualRecognition
 */
class VisualRecognitionPlugin extends IntegrationProviderPlugin
{
	const PLUGIN_NAME = 'visualRecognition';
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
		return 'VisualRecognitionProviderType';
	}
	
	/*
	 * @return IIntegrationProvider
	 */
	public function getProvider()
	{
		return new VisualRecognitionProvider();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'kIntegrationJobProviderData' && $enumValue == self::getApiValue(VisualRecognitionProviderType::VISUAL_RECOGNITION))
		{
			return 'kVisualRecognitionJobProviderData';
		}
	
		if($baseClass == 'KalturaIntegrationJobProviderData')
		{
			if($enumValue == self::getApiValue(VisualRecognitionProviderType::VISUAL_RECOGNITION) ||
				$enumValue == self::getIntegrationProviderCoreValue(VisualRecognitionProviderType::VISUAL_RECOGNITION))
				return 'KalturaVisualRecognitionJobProviderData';
		}
	
		if($baseClass == 'KIntegrationEngine' || $baseClass == 'KIntegrationCloserEngine')
		{
			if($enumValue == VisualRecognitionProviderType::VISUAL_RECOGNITION)
				return 'KVisualRecognitionEngine';
		}
		if($baseClass == 'IIntegrationProvider' && $enumValue == self::getIntegrationProviderCoreValue(VisualRecognitionProviderType::VISUAL_RECOGNITION))
		{
			return 'VisualRecognitionProvider';
		}
	}
}
