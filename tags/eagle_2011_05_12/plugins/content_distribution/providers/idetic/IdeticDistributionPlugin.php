<?php
/**
 * @package plugins.ideticDistribution
 */
class IdeticDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'ideticDistribution';
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 1;
	const CONTENT_DSTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DSTRIBUTION_VERSION_BUILD = 0;
	
	const IDETIC_REPORT_HANDLER = 'kIdeticDistributionReportHandler';

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
			return array('IdeticDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('IdeticDistributionProviderType');
			
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

		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::IDETIC)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new IdeticDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new IdeticDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new IdeticDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new IdeticDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new IdeticDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new IdeticDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new IdeticDistributionEngine();
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaIdeticDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaIdeticDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::IDETIC)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_IdeticProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;


		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(IdeticDistributionProviderType::IDETIC))
		{
			$reflect = new ReflectionClass('KalturaIdeticDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(IdeticDistributionProviderType::IDETIC))
		{
			$reflect = new ReflectionClass('kIdeticDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(IdeticDistributionProviderType::IDETIC))
			return new KalturaIdeticDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(IdeticDistributionProviderType::IDETIC))
			return new IdeticDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::IDETIC)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'IdeticDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'IdeticDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'IdeticDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'IdeticDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'IdeticDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'IdeticDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'IdeticDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaIdeticDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaIdeticDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::IDETIC)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_IdeticProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_IdeticDistribution_Type_IdeticDistributionProfile';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(IdeticDistributionProviderType::IDETIC))
			return 'KalturaIdeticDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(IdeticDistributionProviderType::IDETIC))
			return 'kIdeticDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(IdeticDistributionProviderType::IDETIC))
			return 'KalturaIdeticDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(IdeticDistributionProviderType::IDETIC))
			return 'IdeticDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return IdeticDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaIdeticDistributionProvider();
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
		// append IDETIC specific report statistics
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array();
//		return array(
//			self::IDETIC_REPORT_HANDLER,
//		);
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
