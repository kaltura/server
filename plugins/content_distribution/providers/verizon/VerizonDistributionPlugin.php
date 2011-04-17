<?php
/**
 * @package plugins.verizonDistribution
 */
class VerizonDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'verizonDistribution';
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 1;
	const CONTENT_DSTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DSTRIBUTION_VERSION_BUILD = 0;
	
	const VERIZON_REPORT_HANDLER = 'kVerizonDistributionReportHandler';

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
			return array('VerizonDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('VerizonDistributionProviderType');
			
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

		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::VERIZON)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new VerizonDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new VerizonDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new VerizonDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new VerizonDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new VerizonDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new VerizonDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new VerizonDistributionEngine();
		
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaVerizonDistributionProfile();

		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaVerizonDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::VERIZON)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_VerizonProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;


		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonDistributionProviderType::VERIZON))
		{
			$reflect = new ReflectionClass('KalturaVerizonDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(VerizonDistributionProviderType::VERIZON))
		{
			$reflect = new ReflectionClass('kVerizonDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonDistributionProviderType::VERIZON))
			return new KalturaVerizonDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonDistributionProviderType::VERIZON))
			return new VerizonDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::VERIZON)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'VerizonDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'VerizonDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'VerizonDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'VerizonDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'VerizonDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'VerizonDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'VerizonDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaVerizonDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaVerizonDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::VERIZON)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_VerizonProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_VerizonDistribution_Type_VerizonDistributionProfile';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonDistributionProviderType::VERIZON))
			return 'KalturaVerizonDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(VerizonDistributionProviderType::VERIZON))
			return 'kVerizonDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonDistributionProviderType::VERIZON))
			return 'KalturaVerizonDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonDistributionProviderType::VERIZON))
			return 'VerizonDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return VerizonDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaVerizonDistributionProvider();
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
		// append VERIZON specific report statistics
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array();
//		return array(
//			self::VERIZON_REPORT_HANDLER,
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
