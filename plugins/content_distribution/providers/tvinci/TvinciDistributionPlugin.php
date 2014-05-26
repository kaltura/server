<?php
/**
 * @package plugins.tvinciDistribution
 */
class TvinciDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider, IKalturaEventConsumers, IKalturaConfigurator
{
	const PLUGIN_NAME = 'tvinciDistribution';
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 1;
	const CONTENT_DSTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DSTRIBUTION_VERSION_BUILD = 0;
	
	const TVINCI_EVENT_CONSUMER = 'kTvinciDistributionEventConsumer';

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
			return array('TvinciDistributionProviderType');
	
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
			if($baseClass == 'IDistributionEngineCloseDelete')
				return new TvinciDistributionEngineSelector();
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return new TvinciDistributionEngineSelector();
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return new TvinciDistributionEngineSelector();
					
			if($baseClass == 'IDistributionEngineDelete')
				return new TvinciDistributionEngineSelector();
					
			if($baseClass == 'IDistributionEngineReport')
				return new TvinciDistributionEngineSelector();
					
			if($baseClass == 'IDistributionEngineSubmit')
				return new TvinciDistributionEngineSelector();
					
			if($baseClass == 'IDistributionEngineUpdate')
				return new TvinciDistributionEngineSelector();
		
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
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'TvinciDistributionEngineSelector';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'TvinciDistributionEngineSelector';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'TvinciDistributionEngineSelector';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'TvinciDistributionEngineSelector';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'TvinciDistributionEngineSelector';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'TvinciDistributionEngineSelector';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'TvinciDistributionEngineSelector';
		
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
	    // append Tvinci specific report statistics
	    $distributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
		$mrss->addChild('allow_comments', $distributionProfile->getAllowComments());
		$mrss->addChild('allow_responses', $distributionProfile->getAllowResponses());
		$mrss->addChild('allow_ratings', $distributionProfile->getAllowRatings());
		$mrss->addChild('allow_embedding', $distributionProfile->getAllowEmbedding());
		$mrss->addChild('commerical_policy', $distributionProfile->getCommercialPolicy());
		$mrss->addChild('ugc_policy', $distributionProfile->getUgcPolicy());
		$mrss->addChild('default_category', $distributionProfile->getDefaultCategory());
		$mrss->addChild('target', $distributionProfile->getTarget());
		$mrss->addChild('notification_email', $distributionProfile->getNotificationEmail());
		$mrss->addChild('account_username', $distributionProfile->getUsername());
		$mrss->addChild('ad_server_partner_id', $distributionProfile->getAdServerPartnerId());
		$mrss->addChild('allow_pre_roll_ads', $distributionProfile->getAllowPreRollAds());
		$mrss->addChild('allow_post_roll_ads', $distributionProfile->getAllowPostRollAds());		
		$mrss->addChild('claim_type', $distributionProfile->getClaimType());
		$mrss->addChild('instream_standard', $distributionProfile->getInstreamStandard());
	}
	
	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::TVINCI_EVENT_CONSUMER,
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
