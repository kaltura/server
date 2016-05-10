<?php
/**
 * @package plugins.metroPcsDistribution
 */
class MetroPcsDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'metroPcsDistribution';
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
			return array('MetroPcsDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('MetroPcsDistributionProviderType');
			
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
		// client side apps like batch and admin console
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::METRO_PCS)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new MetroPcsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new MetroPcsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new MetroPcsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new MetroPcsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new MetroPcsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new MetroPcsDistributionEngine();
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaMetroPcsDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaMetroPcsDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::METRO_PCS)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_MetroPcsProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(MetroPcsDistributionProviderType::METRO_PCS))
		{
			$reflect = new ReflectionClass('KalturaMetroPcsDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(MetroPcsDistributionProviderType::METRO_PCS))
		{
			$reflect = new ReflectionClass('kMetroPcsDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MetroPcsDistributionProviderType::METRO_PCS))
			return new KalturaMetroPcsDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MetroPcsDistributionProviderType::METRO_PCS))
			return new MetroPcsDistributionProfile();
			
		return null;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		// client side apps like batch and admin console
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::METRO_PCS)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'MetroPcsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'MetroPcsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'MetroPcsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'MetroPcsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'MetroPcsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'MetroPcsDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaMetroPcsDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaMetroPcsDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::METRO_PCS)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_MetroPcsProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_MetroPcsDistribution_Type_MetroPcsDistributionProfile';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(MetroPcsDistributionProviderType::METRO_PCS))
			return 'KalturaMetroPcsDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(MetroPcsDistributionProviderType::METRO_PCS))
			return 'kMetroPcsDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MetroPcsDistributionProviderType::METRO_PCS))
			return 'KalturaMetroPcsDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MetroPcsDistributionProviderType::METRO_PCS))
			return 'MetroPcsDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return MetroPcsDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaMetroPcsDistributionProvider();
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
		$distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
		/* @var $distributionProfile MetroPcsDistributionProfile */
		//$mrss->addChild('provider_name', $distributionProfile->getProviderName());
		$mrss->addChild('provider_id', $distributionProfile->getProviderId());		
		$mrss->addChild('copyright', $distributionProfile->getCopyright());
		$mrss->addChild('entitlements', $distributionProfile->getEntitlements());
		$mrss->addChild('rating', $distributionProfile->getRating());
		$mrss->addChild('item_type', $distributionProfile->getItemType());		
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
}
