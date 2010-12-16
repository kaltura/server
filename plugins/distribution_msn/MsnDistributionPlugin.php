<?php
class MsnDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider
{
	const PLUGIN_NAME = 'msnDistribution';
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
			return array('MsnDistributionProviderType');
			
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
		if($baseClass == 'IDistributionEngineCloseDelete' && $enumValue == MsnDistributionProviderType::MSN)
			return new MsnDistributionEngine();
				
		if($baseClass == 'IDistributionEngineCloseReport' && $enumValue == MsnDistributionProviderType::MSN)
			return new MsnDistributionEngine();
				
		if($baseClass == 'IDistributionEngineCloseSubmit' && $enumValue == MsnDistributionProviderType::MSN)
			return new MsnDistributionEngine();
				
		if($baseClass == 'IDistributionEngineCloseUpdate' && $enumValue == MsnDistributionProviderType::MSN)
			return new MsnDistributionEngine();
				
		if($baseClass == 'IDistributionEngineDelete' && $enumValue == MsnDistributionProviderType::MSN)
			return new MsnDistributionEngine();
				
		if($baseClass == 'IDistributionEngineReport' && $enumValue == MsnDistributionProviderType::MSN)
			return new MsnDistributionEngine();
				
		if($baseClass == 'IDistributionEngineSubmit' && $enumValue == MsnDistributionProviderType::MSN)
			return new MsnDistributionEngine();
				
		if($baseClass == 'IDistributionEngineUpdate' && $enumValue == MsnDistributionProviderType::MSN)
			return new MsnDistributionEngine();
	
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == MsnDistributionProviderType::get()->coreValue(MsnDistributionProviderType::MSN))
		{
			$reflect = new ReflectionClass('KalturaMsnDistributionJobProviderData');
			return $reflect->newInstance($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == MsnDistributionProviderType::get()->apiValue(MsnDistributionProviderType::MSN))
		{
			$reflect = new ReflectionClass('kMsnDistributionJobProviderData');
			return $reflect->newInstance($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == MsnDistributionProviderType::get()->coreValue(MsnDistributionProviderType::MSN))
			return new KalturaMsnDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == MsnDistributionProviderType::get()->coreValue(MsnDistributionProviderType::MSN))
			return new MsnDistributionProfile();
			
		return null;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'IDistributionEngineCloseDelete' && $enumValue == MsnDistributionProviderType::MSN)
			return 'MsnDistributionEngine';
				
		if($baseClass == 'IDistributionEngineCloseReport' && $enumValue == MsnDistributionProviderType::MSN)
			return 'MsnDistributionEngine';
				
		if($baseClass == 'IDistributionEngineCloseSubmit' && $enumValue == MsnDistributionProviderType::MSN)
			return 'MsnDistributionEngine';
				
		if($baseClass == 'IDistributionEngineCloseUpdate' && $enumValue == MsnDistributionProviderType::MSN)
			return 'MsnDistributionEngine';
				
		if($baseClass == 'IDistributionEngineDelete' && $enumValue == MsnDistributionProviderType::MSN)
			return 'MsnDistributionEngine';
				
		if($baseClass == 'IDistributionEngineReport' && $enumValue == MsnDistributionProviderType::MSN)
			return 'MsnDistributionEngine';
				
		if($baseClass == 'IDistributionEngineSubmit' && $enumValue == MsnDistributionProviderType::MSN)
			return 'MsnDistributionEngine';
				
		if($baseClass == 'IDistributionEngineUpdate' && $enumValue == MsnDistributionProviderType::MSN)
			return 'MsnDistributionEngine';
	
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == MsnDistributionProviderType::get()->coreValue(MsnDistributionProviderType::MSN))
			return 'KalturaMsnDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == MsnDistributionProviderType::get()->apiValue(MsnDistributionProviderType::MSN))
			return 'kMsnDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == MsnDistributionProviderType::get()->coreValue(MsnDistributionProviderType::MSN))
			return 'KalturaMsnDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == MsnDistributionProviderType::get()->coreValue(MsnDistributionProviderType::MSN))
			return 'MsnDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return MsnDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaMsnDistributionProvider();
		$distributionProvider->fromObject(self::getProvider());
		return $distributionProvider;
	}
}
