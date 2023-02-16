<?php
/**
 * @package plugins.cortexApiDistribution
 */
class CortexApiDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'cortexApiDistribution';
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 2;
	const CONTENT_DSTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DSTRIBUTION_VERSION_BUILD = 0;
	
	const GOOGLE_APP_ID = 'cortexapi';
	
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
			return array('CortexApiDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('CortexApiDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::CORTEX_API)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new CortexApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new CortexApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new CortexApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new CortexApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new CortexApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new CortexApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new CortexApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineEnable')
				return new CortexApiDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDisable')
				return new CortexApiDistributionEngine();
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaCortexApiDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaCortexApiDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::CORTEX_API)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_CortexApiProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(CortexApiDistributionProviderType::CORTEX_API))
		{
			$reflect = new ReflectionClass('KalturaCortexApiDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(CortexApiDistributionProviderType::CORTEX_API))
		{
			$reflect = new ReflectionClass('kCortexApiDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(CortexApiDistributionProviderType::CORTEX_API))
			return new KalturaCortexApiDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(CortexApiDistributionProviderType::CORTEX_API))
			return new CortexApiDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::CORTEX_API)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'CortexApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'CortexApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'CortexApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'CortexApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'CortexApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'CortexApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'CortexApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineEnable')
				return 'CortexApiDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDisable')
				return 'CortexApiDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaCortexApiDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaCortexApiDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::CORTEX_API)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_CortexApiProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_CortexApiDistribution_Type_CortexApiDistributionProfile';
		}
		
		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(CortexApiDistributionProviderType::CORTEX_API))
			return 'KalturaCortexApiDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(CortexApiDistributionProviderType::CORTEX_API))
			return 'kCortexApiDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(CortexApiDistributionProviderType::CORTEX_API))
			return 'KalturaCortexApiDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(CortexApiDistributionProviderType::CORTEX_API))
			return 'CortexApiDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return CortexApiDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaCortexApiDistributionProvider();
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
	    	// append Cortex specific report statistics
		/** @var CortexApiDistributionProfile $distributionProfile */
		$distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
		$mrss->addChild('host', $distributionProfile->getHost());
		$mrss->addChild('username', $distributionProfile->getUsername());
		$mrss->addChild('password', $distributionProfile->getPassword());
		$mrss->addChild('folderrecordid', $distributionProfile->getFolderRecordID());
		$mrss->addChild('metadataprofileid', $distributionProfile->getMetadataProfileId());
		$mrss->addChild('metadataprofileidpushing', $distributionProfile->getMetadataProfileIdPushing());
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
