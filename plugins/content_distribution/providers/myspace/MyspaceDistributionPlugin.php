<?php
class MyspaceDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'myspaceDistribution';
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 1;
	const CONTENT_DSTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DSTRIBUTION_VERSION_BUILD = 0;
	
	const MYSPACE_REPORT_HANDLER = 'kMyspaceDistributionReportHandler';

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
			return array('MyspaceDistributionProviderType');
			
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

		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::MYSPACE)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new MyspaceDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new MyspaceDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new MyspaceDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new MyspaceDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new MyspaceDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new MyspaceDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new MyspaceDistributionEngine();
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_MyspaceProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaMyspaceDistributionProfile();

		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaMyspaceDistributionJobProviderData();
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;


		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(MyspaceDistributionProviderType::MYSPACE))
		{
			$reflect = new ReflectionClass('KalturaMyspaceDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(MyspaceDistributionProviderType::MYSPACE))
		{
			$reflect = new ReflectionClass('kMyspaceDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MyspaceDistributionProviderType::MYSPACE))
			return new KalturaMyspaceDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MyspaceDistributionProviderType::MYSPACE))
			return new MyspaceDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::MYSPACE)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'MyspaceDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'MyspaceDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'MyspaceDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'MyspaceDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'MyspaceDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'MyspaceDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'MyspaceDistributionEngine';
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_MyspaceProfileConfiguration';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaMyspaceDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaMyspaceDistributionJobProviderData';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(MyspaceDistributionProviderType::MYSPACE))
			return 'KalturaMyspaceDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(MyspaceDistributionProviderType::MYSPACE))
			return 'kMyspaceDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MyspaceDistributionProviderType::MYSPACE))
			return 'KalturaMyspaceDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MyspaceDistributionProviderType::MYSPACE))
			return 'MyspaceDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return MyspaceDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaMyspaceDistributionProvider();
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
		// append MYSPACE specific report statistics
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::MYSPACE_REPORT_HANDLER,
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
