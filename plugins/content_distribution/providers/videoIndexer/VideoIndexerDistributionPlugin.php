<?php
/**
 * @package plugins.videoIndexerDistribution
 */
class VideoIndexerDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'videoIndexerDistribution';
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
			return array('VideoIndexerDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('VideoIndexerDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::VIDEOINDEXER)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new VideoIndexerDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new VideoIndexerDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new VideoIndexerDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new VideoIndexerDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new VideoIndexerDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new VideoIndexerDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new VideoIndexerDistributionEngine();
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaVideoIndexerDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaVideoIndexerDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::VIDEOINDEXER)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_VideoIndexerProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(VideoIndexerDistributionProviderType::VIDEOINDEXER))
		{
			$reflect = new ReflectionClass('KalturaVideoIndexerDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(VideoIndexerDistributionProviderType::VIDEOINDEXER))
		{
			$reflect = new ReflectionClass('kVideoIndexerDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VideoIndexerDistributionProviderType::VIDEOINDEXER))
			return new KalturaVideoIndexerDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VideoIndexerDistributionProviderType::VIDEOINDEXER))
			return new VideoIndexerDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::VIDEOINDEXER)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'VideoIndexerDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'VideoIndexerDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'VideoIndexerDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'VideoIndexerDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'VideoIndexerDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'VideoIndexerDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'VideoIndexerDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaVideoIndexerDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaVideoIndexerDistributionJobProviderData';
		}

		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::VIDEOINDEXER)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_VideoIndexerProfileConfiguration';

			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_VideoIndexerDistribution_Type_VideoIndexerDistributionProfile';
		}

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(VideoIndexerDistributionProviderType::VIDEOINDEXER))
			return 'KalturaVideoIndexerDistributionJobProviderData';

		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(VideoIndexerDistributionProviderType::VIDEOINDEXER))
			return 'kVideoIndexerDistributionJobProviderData';

		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VideoIndexerDistributionProviderType::VIDEOINDEXER))
			return 'KalturaVideoIndexerDistributionProfile';

		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VideoIndexerDistributionProviderType::VIDEOINDEXER))
			return 'VideoIndexerDistributionProfile';

		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return VideoIndexerDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaVideoIndexerDistributionProvider();
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
