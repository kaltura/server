<?php
/**
 * @package plugins.cielo24
 */
class Cielo24Plugin extends IntegrationProviderPlugin implements IKalturaEventConsumers
{
	const PLUGIN_NAME = 'cielo24';
	const FLOW_MANAGER = 'kCielo24FlowManager';
	
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
		return 'Cielo24IntegrationProviderType';
	}
	
	/*
	 * @return IIntegrationProvider
	 */
	public function getProvider()
	{
		return new IntegrationCielo24Provider(); 
	}
	
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'kIntegrationJobProviderData' && $enumValue == self::getApiValue(Cielo24IntegrationProviderType::CIELO24))
		{
			return 'kCielo24JobProviderData';
		}
	
		if($baseClass == 'KalturaIntegrationJobProviderData')
		{
			if($enumValue == self::getApiValue(Cielo24IntegrationProviderType::CIELO24) || $enumValue == self::getIntegrationProviderCoreValue(Cielo24IntegrationProviderType::CIELO24))
				return 'KalturaCielo24JobProviderData';
		}
	
		if($baseClass == 'KIntegrationEngine' || $baseClass == 'KIntegrationCloserEngine')
		{
			if($enumValue == KalturaIntegrationProviderType::CIELO24)
				return 'KCielo24IntegrationEngine';
		}
		if($baseClass == 'IIntegrationProvider' && $enumValue == self::getIntegrationProviderCoreValue(Cielo24IntegrationProviderType::CIELO24))
		{
			return 'IntegrationCielo24Provider';
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
	
	public static function getClientHelper($username, $password, $baseUrl = null)
	{
		return new Cielo24ClientHelper($username, $password, $baseUrl);
	}
	
	/**
	 * @return Cielo24Options
	 */	
	public static function getPartnerCielo24Options($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
			return null;
		return $partner->getFromCustomData(Cielo24IntegrationProviderType::CIELO24);
	}
	
	public static function setPartnerCielo24Options($partnerId, Cielo24Options $options)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
			return;
		$partner->putInCustomData(Cielo24IntegrationProviderType::CIELO24, $options);
		$partner->save();
	}
}
