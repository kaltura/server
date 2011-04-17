<?php
/**
 * @package plugins.freewheelDistribution
 */
class FreewheelDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'freewheelDistribution';
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 1;
	const CONTENT_DSTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DSTRIBUTION_VERSION_BUILD = 0;
	
	const FREEWHEEL_REPORT_HANDLER = 'kFreewheelDistributionReportHandler';

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
			return array('FreewheelDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('FreewheelDistributionProviderType');
			
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

		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::FREEWHEEL)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new FreewheelDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new FreewheelDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new FreewheelDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new FreewheelDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new FreewheelDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new FreewheelDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new FreewheelDistributionEngine();
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaFreewheelDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaFreewheelDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::FREEWHEEL)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_FreewheelProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;


		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(FreewheelDistributionProviderType::FREEWHEEL))
		{
			$reflect = new ReflectionClass('KalturaFreewheelDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(FreewheelDistributionProviderType::FREEWHEEL))
		{
			$reflect = new ReflectionClass('kFreewheelDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(FreewheelDistributionProviderType::FREEWHEEL))
			return new KalturaFreewheelDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(FreewheelDistributionProviderType::FREEWHEEL))
			return new FreewheelDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::FREEWHEEL)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'FreewheelDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'FreewheelDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'FreewheelDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'FreewheelDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'FreewheelDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'FreewheelDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'FreewheelDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaFreewheelDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaFreewheelDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::FREEWHEEL)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_FreewheelProfileConfiguration';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(FreewheelDistributionProviderType::FREEWHEEL))
			return 'KalturaFreewheelDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(FreewheelDistributionProviderType::FREEWHEEL))
			return 'kFreewheelDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(FreewheelDistributionProviderType::FREEWHEEL))
			return 'KalturaFreewheelDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(FreewheelDistributionProviderType::FREEWHEEL))
			return 'FreewheelDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return FreewheelDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaFreewheelDistributionProvider();
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
		// append FREEWHEEL specific report statistics
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
			return array();
			
//		return array(
//			self::FREEWHEEL_REPORT_HANDLER,
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
