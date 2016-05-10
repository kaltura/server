<?php
/**
 * @package plugins.doubleClickDistribution
 */
class DoubleClickDistributionPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaContentDistributionProvider, IKalturaEventConsumers, IKalturaServices
{
	const PLUGIN_NAME = 'doubleClickDistribution';
	const COMCAST_MRSS_EVENT_CONSUMER = "kDoubleClickFlowManager";
	const CONTENT_DSTRIBUTION_VERSION_MAJOR = 2;
	const CONTENT_DSTRIBUTION_VERSION_MINOR = 0;
	const CONTENT_DSTRIBUTION_VERSION_BUILD = 0;

	const DEPENDENTS_ON_PLUGIN_NAME_CUE_POINT = 'cuePoint';

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
			
		$dependency1 = new KalturaDependency(ContentDistributionPlugin::getPluginName(), $contentDistributionVersion);
		$dependency2 = new KalturaDependency(DoubleClickDistributionPlugin::DEPENDENTS_ON_PLUGIN_NAME_CUE_POINT);
		return array($dependency1, $dependency2);
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
			return array('DoubleClickDistributionProviderType');
	
		if($baseEnumName == 'DistributionProviderType')
			return array('DoubleClickDistributionProviderType');
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::DOUBLECLICK)
		{
			if($baseClass == 'KalturaDistributionProfile')
				return new KalturaDoubleClickDistributionProfile();
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::DOUBLECLICK)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
			{
				$reflect = new ReflectionClass('Form_DoubleClickProfileConfiguration');
				return $reflect->newInstanceArgs($constructorArgs);
			}
		}
		
		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(DoubleClickDistributionProviderType::DOUBLECLICK))
		{
			$reflect = new ReflectionClass('KalturaDoubleClickDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(DoubleClickDistributionProviderType::DOUBLECLICK))
		{
			$reflect = new ReflectionClass('kDoubleClickDistributionJobProviderData');
			return $reflect->newInstanceArgs($constructorArgs);
		}
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(DoubleClickDistributionProviderType::DOUBLECLICK))
			return new KalturaDoubleClickDistributionProfile();
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(DoubleClickDistributionProviderType::DOUBLECLICK))
			return new DoubleClickDistributionProfile();
			
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
		if (class_exists('KalturaClient') && $enumValue == KalturaDistributionProviderType::DOUBLECLICK)
		{
			if($baseClass == 'IDistributionEngineCloseDelete')
				return 'DoubleClickDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseSubmit')
				return 'DoubleClickDistributionEngine';
					
			if($baseClass == 'IDistributionEngineCloseUpdate')
				return 'DoubleClickDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDelete')
				return 'DoubleClickDistributionEngine';
					
			if($baseClass == 'IDistributionEngineReport')
				return 'DoubleClickDistributionEngine';
					
			if($baseClass == 'IDistributionEngineSubmit')
				return 'DoubleClickDistributionEngine';
					
			if($baseClass == 'IDistributionEngineUpdate')
				return 'DoubleClickDistributionEngine';
		
			if($baseClass == 'IDistributionEngineEnable')
				return 'DoubleClickDistributionEngine';
					
			if($baseClass == 'IDistributionEngineDisable')
				return 'DoubleClickDistributionEngine';
		
			if($baseClass == 'KalturaDistributionProfile')
				return 'KalturaDoubleClickDistributionProfile';
		
			if($baseClass == 'KalturaDistributionJobProviderData')
				return 'KalturaDoubleClickDistributionJobProviderData';
		}
		
		if (class_exists('Kaltura_Client_Client') && $enumValue == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::DOUBLECLICK)
		{
			if($baseClass == 'Form_ProviderProfileConfiguration')
				return 'Form_DoubleClickProfileConfiguration';
				
			if($baseClass == 'Kaltura_Client_ContentDistribution_Type_DistributionProfile')
				return 'Kaltura_Client_DoubleClickDistribution_Type_DoubleClickDistributionProfile';
		}
		
		if($baseClass == 'KalturaDistributionJobProviderData' && $enumValue == self::getDistributionProviderTypeCoreValue(DoubleClickDistributionProviderType::DOUBLECLICK))
			return 'KalturaDoubleClickDistributionJobProviderData';
	
		if($baseClass == 'kDistributionJobProviderData' && $enumValue == self::getApiValue(DoubleClickDistributionProviderType::DOUBLECLICK))
			return 'kDoubleClickDistributionJobProviderData';
	
		if($baseClass == 'KalturaDistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(DoubleClickDistributionProviderType::DOUBLECLICK))
			return 'KalturaDoubleClickDistributionProfile';
			
		if($baseClass == 'DistributionProfile' && $enumValue == self::getDistributionProviderTypeCoreValue(DoubleClickDistributionProviderType::DOUBLECLICK))
			return 'DoubleClickDistributionProfile';
			
		return null;
	}
	
	/**
	 * Return a distribution provider instance
	 * 
	 * @return IDistributionProvider
	 */
	public static function getProvider()
	{
		return DoubleClickDistributionProvider::get();
	}
	
	/**
	 * Return an API distribution provider instance
	 * 
	 * @return KalturaDistributionProvider
	 */
	public static function getKalturaProvider()
	{
		$distributionProvider = new KalturaDoubleClickDistributionProvider();
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
		/* @var $distributionProfile DoubleClickDistributionProfile */
		$mrss->addChild('ChannelTitle', htmlentities($distributionProfile->getChannelTitle()));
		$mrss->addChild('ChannelDescription', htmlentities($distributionProfile->getChannelDescription()));
		$mrss->addChild('ChannelLink', htmlentities($distributionProfile->getChannelLink()));
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
			self::COMCAST_MRSS_EVENT_CONSUMER,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		return array(
			'doubleClick' => 'DoubleClickService'
		);
	}
}
