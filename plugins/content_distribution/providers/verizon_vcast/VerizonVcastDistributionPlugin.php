<?php
/**
 * @package plugins.verizonVcastDistribution
 */
class VerizonVcastDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'verizonVcastDistribution';
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
			return array('VerizonVcastDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('VerizonVcastDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::VERIZON_VCAST)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new VerizonVcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new VerizonVcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new VerizonVcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new VerizonVcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new VerizonVcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new VerizonVcastDistributionEngine();
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaVerizonVcastDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaVerizonVcastDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::VERIZON_VCAST)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_VerizonVcastProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonVcastDistributionProviderType::VERIZON_VCAST))
		{
			$reflect = new ReflectionClass('KalturaVerizonVcastDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(VerizonVcastDistributionProviderType::VERIZON_VCAST))
		{
			$reflect = new ReflectionClass('kVerizonVcastDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonVcastDistributionProviderType::VERIZON_VCAST))
			return new KalturaVerizonVcastDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonVcastDistributionProviderType::VERIZON_VCAST))
			return new VerizonVcastDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::VERIZON_VCAST)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'VerizonVcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'VerizonVcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'VerizonVcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'VerizonVcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'VerizonVcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'VerizonVcastDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaVerizonVcastDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaVerizonVcastDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::VERIZON_VCAST)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_VerizonVcastProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_VerizonVcastDistribution_Type_VerizonVcastDistributionProfile';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonVcastDistributionProviderType::VERIZON_VCAST))
			return 'KalturaVerizonVcastDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(VerizonVcastDistributionProviderType::VERIZON_VCAST))
			return 'kVerizonVcastDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonVcastDistributionProviderType::VERIZON_VCAST))
			return 'KalturaVerizonVcastDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(VerizonVcastDistributionProviderType::VERIZON_VCAST))
			return 'VerizonVcastDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return VerizonVcastDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaVerizonVcastDistributionProvider();
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
		$distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
		/* @var $distributionProfile VerizonVcastDistributionProfile */
		$mrss->addChild('ProviderName', $distributionProfile->getProviderName());
		$mrss->addChild('ProviderId', $distributionProfile->getProviderId());
		$mrss->addChild('Entitlement', $distributionProfile->getEntitlement());
		$mrss->addChild('Priority', $distributionProfile->getPriority());
		$mrss->addChild('AllowStreaming', $distributionProfile->getAllowStreaming());
		$mrss->addChild('StreamingPriceCode', $distributionProfile->getStreamingPriceCode());
		$mrss->addChild('AllowDownload', $distributionProfile->getAllowDownload());
		$mrss->addChild('DownloadPriceCode', $distributionProfile->getDownloadPriceCode());
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
