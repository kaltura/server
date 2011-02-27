<?php
/**
 * @package plugins.youTubeDistribution
 */
class YouTubeDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'youTubeDistribution';
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
			return array('YouTubeDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::YOUTUBE)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new YouTubeDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new YouTubeDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new YouTubeDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new YouTubeDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new YouTubeDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new YouTubeDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new YouTubeDistributionEngine();
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_YouTubeProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaYouTubeDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaYouTubeDistributionJobProviderData();
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(YouTubeDistributionProviderType::YOUTUBE))
		{
			$reflect = new ReflectionClass('KalturaYouTubeDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(YouTubeDistributionProviderType::YOUTUBE))
		{
			$reflect = new ReflectionClass('kYouTubeDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(YouTubeDistributionProviderType::YOUTUBE))
			return new KalturaYouTubeDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(YouTubeDistributionProviderType::YOUTUBE))
			return new YouTubeDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::YOUTUBE)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'YouTubeDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'YouTubeDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'YouTubeDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'YouTubeDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'YouTubeDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'YouTubeDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'YouTubeDistributionEngine';
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_YouTubeProfileConfiguration';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaYouTubeDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaYouTubeDistributionJobProviderData';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(YouTubeDistributionProviderType::YOUTUBE))
			return 'KalturaYouTubeDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(YouTubeDistributionProviderType::YOUTUBE))
			return 'kYouTubeDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(YouTubeDistributionProviderType::YOUTUBE))
			return 'KalturaYouTubeDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(YouTubeDistributionProviderType::YOUTUBE))
			return 'YouTubeDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return YouTubeDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaYouTubeDistributionProvider();
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
