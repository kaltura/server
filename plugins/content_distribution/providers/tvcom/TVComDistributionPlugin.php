<?php
/**
 * @package plugins.tvComDistribution
 */
class TVComDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider, IKalturaEventConsumers, IKalturaServices
{
	const PLUGIN_NAME = 'tvComDistribution';
	const TVCOM_EVENT_CONSUMER = "kTVComFlowManager";
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
			return array('TVComDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('TVComDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::TVCOM)
		{
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaTVComDistributionProfile();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::TVCOM)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_TVComProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(TVComDistributionProviderType::TVCOM))
			return new KalturaTVComDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(TVComDistributionProviderType::TVCOM))
			return new TVComDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::TVCOM)
		{
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaTVComDistributionProfile';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::TVCOM)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_TVComProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_TvComDistribution_Type_TVComDistributionProfile';
		}
		
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(TVComDistributionProviderType::TVCOM))
			return 'KalturaTVComDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(TVComDistributionProviderType::TVCOM))
			return 'TVComDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return TVComDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaTVComDistributionProvider();
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
		// append comcast mrss specific configuration
		$distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
		/* @var $distributionProfile TVComDistributionProfile */ 
		$mrss->addChild('feed_copyright', $distributionProfile->getFeedCopyright());
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
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::TVCOM_EVENT_CONSUMER,
		);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		return array(
			'tvCom' => 'TVComService'
		);
	}
}
