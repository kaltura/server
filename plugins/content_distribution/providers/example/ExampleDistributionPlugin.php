<?php
/**
 * @package plugins.exampleDistribution
 */
class ExampleDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'exampleDistribution';
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
			return array('ExampleDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::EXAMPLE)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new ExampleDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new ExampleDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new ExampleDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new ExampleDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new ExampleDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new ExampleDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new ExampleDistributionEngine();
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_ExampleProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaExampleDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaExampleDistributionJobProviderData();
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(ExampleDistributionProviderType::EXAMPLE))
		{
			$reflect = new ReflectionClass('KalturaExampleDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(ExampleDistributionProviderType::EXAMPLE))
		{
			$reflect = new ReflectionClass('kExampleDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(ExampleDistributionProviderType::EXAMPLE))
			return new KalturaExampleDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(ExampleDistributionProviderType::EXAMPLE))
			return new ExampleDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::EXAMPLE)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'ExampleDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'ExampleDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'ExampleDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'ExampleDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'ExampleDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'ExampleDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'ExampleDistributionEngine';
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_ExampleProfileConfiguration';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaExampleDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaExampleDistributionJobProviderData';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(ExampleDistributionProviderType::EXAMPLE))
			return 'KalturaExampleDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(ExampleDistributionProviderType::EXAMPLE))
			return 'kExampleDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(ExampleDistributionProviderType::EXAMPLE))
			return 'KalturaExampleDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(ExampleDistributionProviderType::EXAMPLE))
			return 'ExampleDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return ExampleDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaExampleDistributionProvider();
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
