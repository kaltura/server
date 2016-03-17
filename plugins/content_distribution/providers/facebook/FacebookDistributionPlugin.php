<?php
/**
 * @package plugins.facebookDistribution
 */
class FacebookDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider, IKalturaConfigurator
{
	const PLUGIN_NAME = 'facebookDistribution';
	const CONTENT_DISTRIBUTION_VERSION_MAJOR = 1;
	const CONTENT_DISTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DISTRIBUTION_VERSION_BUILD = 0;
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function dependsOn()
	{
		$contentDistributionVersion = new KalturaVersion(
			self::CONTENT_DISTRIBUTION_VERSION_MAJOR,
			self::CONTENT_DISTRIBUTION_VERSION_MINOR,
			self::CONTENT_DISTRIBUTION_VERSION_BUILD);
			
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
			return array('FacebookDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('FacebookDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::FACEBOOK)
		{
			if(in_array($baseClass, array('IDistributionEngineSubmit', 'IDistributionEngineDelete', 'IDistributionEngineUpdate')))
				return new FacebookDistributionEngine();

			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaFacebookDistributionProfile();
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return new KalturaFacebookDistributionJobProviderData();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::FACEBOOK)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_FacebookProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(FacebookDistributionProviderType::FACEBOOK))
		{
			$reflect = new ReflectionClass('KalturaFacebookDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(FacebookDistributionProviderType::FACEBOOK))
		{
			$reflect = new ReflectionClass('kFacebookDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(FacebookDistributionProviderType::FACEBOOK))
			return new KalturaFacebookDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(FacebookDistributionProviderType::FACEBOOK))
			return new FacebookDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::FACEBOOK)
		{

			if(in_array($baseClass, array('IDistributionEngineSubmit', 'IDistributionEngineDelete', 'IDistributionEngineUpdate'))) {
				return 'FacebookDistributionEngine';
			}
					
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaFacebookDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaFacebookDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::FACEBOOK)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_FacebookProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_FacebookDistribution_Type_FacebookDistributionProfile';
		}
		
		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(FacebookDistributionProviderType::FACEBOOK))
			return 'KalturaFacebookDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(FacebookDistributionProviderType::FACEBOOK))
			return 'kFacebookDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(FacebookDistributionProviderType::FACEBOOK))
			return 'KalturaFacebookDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(FacebookDistributionProviderType::FACEBOOK))
			return 'FacebookDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return FacebookDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaFacebookDistributionProvider();
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
		if ($distributionProfile && $distributionProfile instanceof KalturaFacebookDistributionProfile)
		{
			$mrss->addChild(FacebookDistributionField::CALL_TO_ACTION_TYPE, $distributionProfile->getCallToActionType());
			$mrss->addChild(FacebookDistributionField::CALL_TO_ACTION_LINK, $distributionProfile->getCallToActionLink());
			$mrss->addChild(FacebookDistributionField::CALL_TO_ACTION_LINK_CAPTION, $distributionProfile->getCallToActionLinkCaption());
			$mrss->addChild(FacebookDistributionField::PLACE, $distributionProfile->getPlace());
			$mrss->addChild(FacebookDistributionField::TAGS, $distributionProfile->getTags());
			$mrss->addChild(FacebookDistributionField::TARGETING, $distributionProfile->getTargeting());
			$mrss->addChild(FacebookDistributionField::FEED_TARGETING, $distributionProfile->getFeedTargeting());
		}
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
