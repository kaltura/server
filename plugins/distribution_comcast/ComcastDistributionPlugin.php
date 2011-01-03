<?php
class ComcastDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'comcastDistribution';
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
	public static function getEnums($baseEnumName)
	{
		if($baseEnumName == 'DistributionProviderType')
			return array('ComcastDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::COMCAST)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new ComcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new ComcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new ComcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new ComcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new ComcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new ComcastDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new ComcastDistributionEngine();
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_ComcastProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaComcastDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaComcastDistributionJobProviderData();
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(ComcastDistributionProviderType::COMCAST))
		{
			$reflect = new ReflectionClass('KalturaComcastDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(ComcastDistributionProviderType::COMCAST))
		{
			$reflect = new ReflectionClass('kComcastDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(ComcastDistributionProviderType::COMCAST))
			return new KalturaComcastDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(ComcastDistributionProviderType::COMCAST))
			return new ComcastDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::COMCAST)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'ComcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'ComcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'ComcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'ComcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'ComcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'ComcastDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'ComcastDistributionEngine';
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_ComcastProfileConfiguration';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaComcastDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaComcastDistributionJobProviderData';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(ComcastDistributionProviderType::COMCAST))
			return 'KalturaComcastDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(ComcastDistributionProviderType::COMCAST))
			return 'kComcastDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(ComcastDistributionProviderType::COMCAST))
			return 'KalturaComcastDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(ComcastDistributionProviderType::COMCAST))
			return 'ComcastDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return ComcastDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaComcastDistributionProvider();
		$distributionProvider->fromObject(self::getProvider());
		return $distributionProvider;
	}
	
	/**
	 * Append provider specific nodes and attributes to the MRSS
	 * 
	 * @param EntryDistribution $entryDistribution
	 * @param SimpleXMLElement $mrss
	 */
	public static function contibuteMRSS(EntryDistribution $entryDistribution, SimpleXMLElement $mrss)
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
