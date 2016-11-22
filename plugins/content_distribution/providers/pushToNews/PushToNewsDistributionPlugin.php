<?php
/**
 * @package plugins.pushToNewsDistribution
 */
class PushToNewsDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'pushToNewsDistribution';
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 2;
	const CONTENT_DSTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DSTRIBUTION_VERSION_BUILD = 0;

	const DATA_FORMAT_JSON = 1;

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
			return array('PushToNewsDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('PushToNewsDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && ($enumValue == KalturaDistributionProviderType::PUSH_TO_NEWS))
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new PushToNewsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new PushToNewsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new PushToNewsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new PushToNewsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new PushToNewsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new PushToNewsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new PushToNewsDistributionEngine();
		
			if($baseClass == 'IDistributionEngineEnable')
				return new PushToNewsDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDisable')
				return new PushToNewsDistributionEngine();
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaPushToNewsDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaPushToNewsDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && ($enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::PUSH_TO_NEWS))
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_PushToNewsProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}

		if($baseClass == 'KalturaDistributionJobProviderData' && ($enumValue == self::getDistributionProviderTypeCoreValue(PushToNewsDistributionProviderType::PUSH_TO_NEWS)))
		{
			$reflect = new ReflectionClass('KalturaPushToNewsDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && ($enumValue == self::getApiValue(PushToNewsDistributionProviderType::PUSH_TO_NEWS)))
		{
			$reflect = new ReflectionClass('kPushToNewsDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && ($enumValue == self::getDistributionProviderTypeCoreValue(PushToNewsDistributionProviderType::PUSH_TO_NEWS)))
			return new KalturaPushToNewsDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && ($enumValue == self::getDistributionProviderTypeCoreValue(PushToNewsDistributionProviderType::PUSH_TO_NEWS)))
			return new PushToNewsDistributionProfile();
			
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
		if (class_exists('KalturaClient') && ($enumValue == KalturaDistributionProviderType::PUSH_TO_NEWS))
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'PushToNewsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'PushToNewsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'PushToNewsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'PushToNewsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'PushToNewsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'PushToNewsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'PushToNewsDistributionEngine';
		
			if($baseClass == 'IDistributionEngineEnable')
				return 'PushToNewsDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDisable')
				return 'PushToNewsDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaPushToNewsDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaPushToNewsDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && ($enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::PUSH_TO_NEWS))
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_PushToNewsProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_PushToNewsDistribution_Type_PushToNewsDistributionProfile';
		}
		
		if($baseClass == 'KalturaDistributionJobProviderData' && ($enumValue == self::getDistributionProviderTypeCoreValue(PushToNewsDistributionProviderType::PUSH_TO_NEWS)))
			return 'KalturaPushToNewsDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && ($enumValue == self::getApiValue(PushToNewsDistributionProviderType::PUSH_TO_NEWS)))
			return 'kPushToNewsDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && ($enumValue == self::getDistributionProviderTypeCoreValue(PushToNewsDistributionProviderType::PUSH_TO_NEWS)))
			return 'KalturaPushToNewsDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && ($enumValue == self::getDistributionProviderTypeCoreValue(PushToNewsDistributionProviderType::PUSH_TO_NEWS)))
			return 'PushToNewsDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return PushToNewsDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaPushToNewsDistributionProvider();
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
