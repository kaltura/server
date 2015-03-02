<?php
/**
 * @package plugins.unicornDistribution
 */
class UnicornDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider, IKalturaConfigurator, IKalturaServices
{
	const PLUGIN_NAME = 'unicornDistribution';
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 2;
	const CONTENT_DSTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DSTRIBUTION_VERSION_BUILD = 0;


	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function dependsOn()
	{
		$contentDistributionVersion = new KalturaVersion(
			self::CONTENT_DSTRIBUTION_VERSION_MAJOR,
			self::CONTENT_DSTRIBUTION_VERSION_MINOR,
			self::CONTENT_DSTRIBUTION_VERSION_BUILD);
			
		$dependency = new KalturaDependency(ContentDistributionPlugin::getPluginName(), $contentDistributionVersion);
		return array($dependency);
	}
	
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
			
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(ContentDistributionPlugin::getPluginName());
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('UnicornDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('UnicornDistributionProviderType');
			
		return array();
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{			
		$objectClass = self::getObjectClass($baseClass, $enumValue);
		
		if (is_null($objectClass)) {
			return null;
		}
		
		if (!is_null($constructorArgs))
		{
			$reflect = new ReflectionClass($objectClass);
			return $reflect->newInstanceArgs($constructorArgs);
		}
		else
		{
			return new $objectClass();
		}
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		// client side apps like batch and admin console
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::UNICORN)
		{										
			if($baseClass == 'IDistributionEngineSubmit')
				return 'UnicornDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'UnicornDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'UnicornDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'UnicornDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'UnicornDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'UnicornDistributionEngine';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::UNICORN)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_UnicornProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_UnicornDistribution_Type_UnicornDistributionProfile';
		}
		
		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(UnicornDistributionProviderType::UNICORN))
			return 'KalturaUnicornDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(UnicornDistributionProviderType::UNICORN))
			return 'kUnicornDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(UnicornDistributionProviderType::UNICORN))
			return 'KalturaUnicornDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(UnicornDistributionProviderType::UNICORN))
			return 'UnicornDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return UnicornDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaUnicornDistributionProvider();
		$distributionProvider->fromObject(self::getProvider());
		return $distributionProvider;
	}
	
	/**
	 * Append provider specific nodes and attributes to the MRSS
	 * 
	 * @param EntryDistribution $entryDistribution
	 * @param SimpleXMLElement $mrss
	 */
	public static function contributeMRSS(EntryDistribution $entryDistribution, SimpleXMLElement $mrss)
	{
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getDistributionProviderTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('DistributionProviderType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		return array(
			'unicorn' => 'UnicornService'
		);
	}
}
