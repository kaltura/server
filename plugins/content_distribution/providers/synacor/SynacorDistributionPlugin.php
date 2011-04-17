<?php
/**
 * @package plugins.synacorDistribution
 */
class SynacorDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'synacorDistribution';
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
		if($baseEnumName == 'DistributionProviderType')
			return array('SynacorDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::SYNACOR)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new SynacorDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new SynacorDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new SynacorDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new SynacorDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new SynacorDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new SynacorDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new SynacorDistributionEngine();
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaSynacorDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaSynacorDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::SYNACOR)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_SynacorProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(SynacorDistributionProviderType::SYNACOR))
		{
			$reflect = new ReflectionClass('KalturaSynacorDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(SynacorDistributionProviderType::SYNACOR))
		{
			$reflect = new ReflectionClass('kSynacorDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(SynacorDistributionProviderType::SYNACOR))
			return new KalturaSynacorDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(SynacorDistributionProviderType::SYNACOR))
			return new SynacorDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::SYNACOR)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'SynacorDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'SynacorDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'SynacorDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'SynacorDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'SynacorDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'SynacorDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'SynacorDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaSynacorDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaSynacorDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::SYNACOR)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_SynacorProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_SynacorDistribution_Type_SynacorDistributionProfile';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(SynacorDistributionProviderType::SYNACOR))
			return 'KalturaSynacorDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(SynacorDistributionProviderType::SYNACOR))
			return 'kSynacorDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(SynacorDistributionProviderType::SYNACOR))
			return 'KalturaSynacorDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(SynacorDistributionProviderType::SYNACOR))
			return 'SynacorDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return SynacorDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaSynacorDistributionProvider();
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
