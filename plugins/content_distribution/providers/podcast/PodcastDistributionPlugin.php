<?php
/**
 * @package plugins.podcastDistribution
 */
class PodcastDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'podcastDistribution';
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 2;
	const CONTENT_DSTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DSTRIBUTION_VERSION_BUILD = 0;
	
	const PODCAST_REPORT_HANDLER = 'kPodcastDistributionReportHandler';

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
			return array('PodcastDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('PodcastDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::PODCAST)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new PodcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new PodcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new PodcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new PodcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new PodcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new PodcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new PodcastDistributionEngine();
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaPodcastDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaPodcastDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::PODCAST)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_PodcastProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(PodcastDistributionProviderType::PODCAST))
		{
			$reflect = new ReflectionClass('KalturaPodcastDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(PodcastDistributionProviderType::PODCAST))
		{
			$reflect = new ReflectionClass('kPodcastDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(PodcastDistributionProviderType::PODCAST))
			return new KalturaPodcastDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(PodcastDistributionProviderType::PODCAST))
			return new PodcastDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::PODCAST)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'PodcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'PodcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'PodcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'PodcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'PodcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'PodcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'PodcastDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaPodcastDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaPodcastDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::PODCAST)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_PodcastProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_PodcastDistribution_Type_PodcastDistributionProfile';
		}
		
		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(PodcastDistributionProviderType::PODCAST))
			return 'KalturaPodcastDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(PodcastDistributionProviderType::PODCAST))
			return 'kPodcastDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(PodcastDistributionProviderType::PODCAST))
			return 'KalturaPodcastDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(PodcastDistributionProviderType::PODCAST))
			return 'PodcastDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return PodcastDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaPodcastDistributionProvider();
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
		// append PODCAST specific report statistics
		$status = $mrss->addChild('status');
		
		$status->addChild('emailed', $entryDistribution->getFromCustomData('emailed'));
		$status->addChild('rated', $entryDistribution->getFromCustomData('rated'));
		$status->addChild('blogged', $entryDistribution->getFromCustomData('blogged'));
		$status->addChild('reviewed', $entryDistribution->getFromCustomData('reviewed'));
		$status->addChild('bookmarked', $entryDistribution->getFromCustomData('bookmarked'));
		$status->addChild('playbackFailed', $entryDistribution->getFromCustomData('playbackFailed'));
		$status->addChild('timeSpent', $entryDistribution->getFromCustomData('timeSpent'));
		$status->addChild('recommended', $entryDistribution->getFromCustomData('recommended'));
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array();
		return array(
			self::PODCAST_REPORT_HANDLER,
		);
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
