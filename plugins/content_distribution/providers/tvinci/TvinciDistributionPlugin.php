<?php
/**
 * @package plugins.tvinciDistribution
 */
class TvinciDistributionPlugin extends KalturaParentContributedPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader,
				IKalturaContentDistributionProvider, IKalturaConfigurator
{
	const PLUGIN_NAME = 'tvinciDistribution';
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
		if(is_null($baseEnumName))
			return array('TvinciDistributionProviderType', 'ParentObjectFeatureType');

		if($baseEnumName == 'ObjectFeatureType')
			return array('ParentObjectFeatureType');

		if($baseEnumName == 'DistributionProviderType')
			return array('TvinciDistributionProviderType');

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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::TVINCI)
		{
			if($baseClass == 'IDistributionEngineDelete')
				return new TvinciDistributionFeedEngine();

			if($baseClass == 'IDistributionEngineReport')
				return new TvinciDistributionFeedEngine();

			if($baseClass == 'IDistributionEngineSubmit')
				return new TvinciDistributionFeedEngine();

			if($baseClass == 'IDistributionEngineUpdate')
				return new TvinciDistributionFeedEngine();

			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaTvinciDistributionProfile();

			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaTvinciDistributionJobProviderData();
		}

		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::TVINCI)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_TvinciProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(TvinciDistributionProviderType::TVINCI))
		{
			$reflect = new ReflectionClass('KalturaTvinciDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}

		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(TvinciDistributionProviderType::TVINCI))
		{
			$reflect = new ReflectionClass('kTvinciDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}

		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(TvinciDistributionProviderType::TVINCI))
			return new KalturaTvinciDistributionProfile();

		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(TvinciDistributionProviderType::TVINCI))
			return new TvinciDistributionProfile();

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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::TVINCI)
		{
			if($baseClass == 'IDistributionEngineDelete')
				return 'TvinciDistributionFeedEngine';

			if($baseClass == 'IDistributionEngineReport')
				return 'TvinciDistributionFeedEngine';

			if($baseClass == 'IDistributionEngineSubmit')
				return 'TvinciDistributionFeedEngine';

			if($baseClass == 'IDistributionEngineUpdate')
				return 'TvinciDistributionFeedEngine';

			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaTvinciDistributionProfile';

			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaTvinciDistributionJobProviderData';
		}

		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::TVINCI)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_TvinciProfileConfiguration';

			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_TvinciDistribution_Type_TvinciDistributionProfile';
		}

		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(TvinciDistributionProviderType::TVINCI))
			return 'KalturaTvinciDistributionJobProviderData';

		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(TvinciDistributionProviderType::TVINCI))
			return 'kTvinciDistributionJobProviderData';

		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(TvinciDistributionProviderType::TVINCI))
			return 'KalturaTvinciDistributionProfile';

		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(TvinciDistributionProviderType::TVINCI))
			return 'TvinciDistributionProfile';

		return null;
	}

	/**
	 * Return a distribution provider instance
	 *
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return TvinciDistributionProvider::get();
	}

	/**
	 * Return an API distribution provider instance
	 *
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaTvinciDistributionProvider();
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

	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');

		return null;
	}

}
