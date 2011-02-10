<?php
/**
 * @package plugins.msnDistribution
 */
class MsnDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'msnDistribution';
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 1;
	const CONTENT_DSTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DSTRIBUTION_VERSION_BUILD = 0;
	
	const MSN_REPORT_HANDLER = 'kMsnDistributionReportHandler';

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
		// client side apps like batch and admin console
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::MSN)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new MsnDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new MsnDistributionEngine();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new MsnDistributionEngine();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new MsnDistributionEngine();
					
			if($baseClass == 'IDistributionEngineReport')
				return new MsnDistributionEngine();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new MsnDistributionEngine();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new MsnDistributionEngine();
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_MsnProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaMsnDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaMsnDistributionJobProviderData();
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(MsnDistributionProviderType::MSN))
		{
			$reflect = new ReflectionClass('KalturaMsnDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(MsnDistributionProviderType::MSN))
		{
			$reflect = new ReflectionClass('kMsnDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MsnDistributionProviderType::MSN))
			return new KalturaMsnDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MsnDistributionProviderType::MSN))
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
		// client side apps like batch and admin console
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::MSN)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'MsnDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'MsnDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'MsnDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'MsnDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'MsnDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'MsnDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'MsnDistributionEngine';
		
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_MsnProfileConfiguration';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaMsnDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaMsnDistributionJobProviderData';
		}
		
		// content distribution does not work in partner services 2 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || kCurrentContext::$ps_vesion != 'ps3')
			return null;

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(MsnDistributionProviderType::MSN))
			return 'KalturaMsnDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(MsnDistributionProviderType::MSN))
			return 'kMsnDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MsnDistributionProviderType::MSN))
			return 'KalturaMsnDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(MsnDistributionProviderType::MSN))
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
	
	/**
	 * Append provider specific nodes and attributes to the MRSS
	 * 
	 * @param EntryDistribution $entryDistribution
	 * @param SimpleXMLElement $mrss
	 */
	public static function contibuteMRSS(EntryDistribution $entryDistribution, SimpleXMLElement $mrss)
	{
		// append MSN specific report statistics
		$status = $mrss->addChild('status');
		
		$status->addChild('emailed', $entryDistribution->getFromCustomData('emailed'));
		$status->addChild('rated', $entryDistribution->getFromCustomData('rated'));
		$status->addChild('blogged', $entryDistribution->getFromCustomData('blogged'));
		$status->addChild('reviewed', $entryDistribution->getFromCustomData('reviewed'));
		$status->addChild('bookmarked', $entryDistribution->getFromCustomData('bookmarked'));
		$status->addChild('playbackFailed', $entryDistribution->getFromCustomData('playbackFailed'));
		$status->addChild('timeSpent', $entryDistribution->getFromCustomData('timeSpent'));
		$status->addChild('recommended', $entryDistribution->getFromCustomData('recommended'));
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::MSN_REPORT_HANDLER,
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
