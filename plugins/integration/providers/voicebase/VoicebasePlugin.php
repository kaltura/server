<?php
/**
 * @package plugins.voicebase
 */
class VoicebasePlugin extends IntegrationProviderPlugin implements IKalturaEventConsumers
{
	const PLUGIN_NAME = 'voicebase';
	const FLOW_MANAGER = 'kVoicebaseFlowManager';
	
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
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::FLOW_MANAGER,
		);
	}
	
	/* (non-PHPdoc)
	 * @see IntegrationProviderPlugin::getIntegrationProviderClassName()
	 */
	public static function getIntegrationProviderClassName()
	{
		return 'VoicebaseIntegrationProviderType';
	}

	/*
	 * @return IIntegrationProvider
	 */
	public function getProvider()
	{
		return new IntegrationVoicebaseProvider(); 
	}
	
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'kIntegrationJobProviderData' && $enumValue == self::getApiValue(VoicebaseIntegrationProviderType::VOICEBASE))
		{
			return 'kVoicebaseJobProviderData';
		}
	
		if($baseClass == 'KalturaIntegrationJobProviderData')
		{
			if($enumValue == self::getApiValue(VoicebaseIntegrationProviderType::VOICEBASE) || $enumValue == self::getIntegrationProviderCoreValue(VoicebaseIntegrationProviderType::VOICEBASE))
				return 'KalturaVoicebaseJobProviderData';
		}
	
		if($baseClass == 'KIntegrationEngine' || $baseClass == 'KIntegrationCloserEngine')
		{
			if($enumValue == KalturaIntegrationProviderType::VOICEBASE)
				return 'KVoicebaseIntegrationEngine';
		}
		if($baseClass == 'IIntegrationProvider' && $enumValue == self::getIntegrationProviderCoreValue(VoicebaseIntegrationProviderType::VOICEBASE))
		{
			return 'IntegrationVoicebaseProvider';
		}
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getProviderTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('IntegrationProviderType', $value);
	}
	
	public static function getClientHelper($apiKey, $apiPassword)
	{
		return new VoicebaseClientHelper($apiKey, $apiPassword);
	}
	
	/**
	 * @return VoicebaseOptions
	 */	
	public static function getPartnerVoicebaseOptions($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
			return null;
		return $partner->getFromCustomData(VoicebaseIntegrationProviderType::VOICEBASE);
	}
	
	public static function setPartnerVoicebaseOptions($partnerId, VoicebaseOptions $options)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
			return;
		$partner->putInCustomData(VoicebaseIntegrationProviderType::VOICEBASE, $options);
		$partner->save();
	}
}
