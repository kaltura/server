<?php
/**
 * @package plugins.dailymotionDistribution
 */
class DailymotionDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'dailymotionDistribution';
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
			return array('DailymotionDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::DAILYMOTION)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new DailymotionDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new DailymotionDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new DailymotionDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new DailymotionDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new DailymotionDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new DailymotionDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new DailymotionDistributionEngine();
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_DailymotionProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaDailymotionDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaDailymotionDistributionJobProviderData();
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(DailymotionDistributionProviderType::DAILYMOTION))
		{
			$reflect = new ReflectionClass('KalturaDailymotionDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(DailymotionDistributionProviderType::DAILYMOTION))
		{
			$reflect = new ReflectionClass('kDailymotionDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(DailymotionDistributionProviderType::DAILYMOTION))
			return new KalturaDailymotionDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(DailymotionDistributionProviderType::DAILYMOTION))
			return new DailymotionDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::DAILYMOTION)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'DailymotionDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'DailymotionDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'DailymotionDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'DailymotionDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'DailymotionDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'DailymotionDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'DailymotionDistributionEngine';
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_DailymotionProfileConfiguration';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaDailymotionDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaDailymotionDistributionJobProviderData';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(DailymotionDistributionProviderType::DAILYMOTION))
			return 'KalturaDailymotionDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(DailymotionDistributionProviderType::DAILYMOTION))
			return 'kDailymotionDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(DailymotionDistributionProviderType::DAILYMOTION))
			return 'KalturaDailymotionDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(DailymotionDistributionProviderType::DAILYMOTION))
			return 'DailymotionDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return DailymotionDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaDailymotionDistributionProvider();
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
