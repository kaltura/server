<?php
/**
 * @package plugins.youtube_apiDistribution
 */
class Youtube_apiDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'youtube_apiDistribution';
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 1;
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
	public static function getEnums($baseEnumName)
	{
		if($baseEnumName == 'DistributionProviderType')
			return array('Youtube_apiDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::YOUTUBE_API)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new Youtube_apiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new Youtube_apiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new Youtube_apiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new Youtube_apiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new Youtube_apiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new Youtube_apiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new Youtube_apiDistributionEngine();
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_Youtube_apiProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaYoutube_apiDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaYoutube_apiDistributionJobProviderData();
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(Youtube_apiDistributionProviderType::YOUTUBE_API))
		{
			$reflect = new ReflectionClass('KalturaYoutube_apiDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(Youtube_apiDistributionProviderType::YOUTUBE_API))
		{
			$reflect = new ReflectionClass('kYoutube_apiDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(Youtube_apiDistributionProviderType::YOUTUBE_API))
			return new KalturaYoutube_apiDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(Youtube_apiDistributionProviderType::YOUTUBE_API))
			return new Youtube_apiDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::YOUTUBE_API)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'Youtube_apiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'Youtube_apiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'Youtube_apiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'Youtube_apiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'Youtube_apiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'Youtube_apiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'Youtube_apiDistributionEngine';
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_Youtube_apiProfileConfiguration';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaYoutube_apiDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaYoutube_apiDistributionJobProviderData';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(Youtube_apiDistributionProviderType::YOUTUBE_API))
			return 'KalturaYoutube_apiDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(Youtube_apiDistributionProviderType::YOUTUBE_API))
			return 'kYoutube_apiDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(Youtube_apiDistributionProviderType::YOUTUBE_API))
			return 'KalturaYoutube_apiDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(Youtube_apiDistributionProviderType::YOUTUBE_API))
			return 'Youtube_apiDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return Youtube_apiDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaYoutube_apiDistributionProvider();
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
}
