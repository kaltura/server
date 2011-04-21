<?php
/**
 * @package plugins.youtubeApiDistribution
 */
class YoutubeApiDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'youtubeApiDistribution';
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
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('YoutubeApiDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('YoutubeApiDistributionProviderType');
			
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
				return new YoutubeApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new YoutubeApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new YoutubeApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new YoutubeApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new YoutubeApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new YoutubeApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new YoutubeApiDistributionEngine();
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaYoutubeApiDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaYoutubeApiDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::YOUTUBE_API)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_YoutubeApiProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(YoutubeApiDistributionProviderType::YOUTUBE_API))
		{
			$reflect = new ReflectionClass('KalturaYoutubeApiDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(YoutubeApiDistributionProviderType::YOUTUBE_API))
		{
			$reflect = new ReflectionClass('kYoutubeApiDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(YoutubeApiDistributionProviderType::YOUTUBE_API))
			return new KalturaYoutubeApiDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(YoutubeApiDistributionProviderType::YOUTUBE_API))
			return new YoutubeApiDistributionProfile();
			
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
				return 'YoutubeApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'YoutubeApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'YoutubeApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'YoutubeApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'YoutubeApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'YoutubeApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'YoutubeApiDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaYoutubeApiDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaYoutubeApiDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::YOUTUBE_API)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_YoutubeApiProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_YoutubeApiDistribution_Type_YoutubeApiDistributionProfile';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(YoutubeApiDistributionProviderType::YOUTUBE_API))
			return 'KalturaYoutubeApiDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(YoutubeApiDistributionProviderType::YOUTUBE_API))
			return 'kYoutubeApiDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(YoutubeApiDistributionProviderType::YOUTUBE_API))
			return 'KalturaYoutubeApiDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(YoutubeApiDistributionProviderType::YOUTUBE_API))
			return 'YoutubeApiDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return YoutubeApiDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaYoutubeApiDistributionProvider();
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
