<?php
/**
 * @package plugins.widevine
 */
class WidevinePlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaServices , IKalturaPermissions, IKalturaObjectLoader, IKalturaEventConsumers, IKalturaTypeExtender, IKalturaSearchDataContributor
{
	const PLUGIN_NAME = 'widevine';
	const WIDEVINE_EVENTS_CONSUMER = 'kWidevineEventsConsumer';
	const WIDEVINE_RESPONSE_TYPE = 'widevine';
	const WIDEVINE_ENABLE_DISTRIBUTION_DATES_SYNC_PERMISSION = 'WIDEVINE_ENABLE_DISTRIBUTION_DATES_SYNC';
	const SEARCH_DATA_SUFFIX = 's';
	
	const REGISTER_ASSET_CGI = '/widevine/cypherpc/sign/cgi-bin/RegisterAsset.cgi';
	const GET_ASSET_CGI = '/widevine/cypherpc/cgi-bin/GetAsset.cgi';
	
	//Default values
	const KALTURA_PROVIDER = 'kaltura';
	const DEFAULT_POLICY = 'default';
	const DEFAULT_LICENSE_START = '1970-01-01 00:00:01';
	const DEFAULT_LICENSE_END = '2033-05-18 00:00:00';
	
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
		
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{	
		if(is_null($baseEnumName))
			return array('WidevineConversionEngineType', 'WidevineAssetType', 'WidevinePermissionName', 'WidevineBatchJobType');		
		if($baseEnumName == 'conversionEngineType')
			return array('WidevineConversionEngineType');
		if($baseEnumName == 'assetType')
			return array('WidevineAssetType');
		if($baseEnumName == 'PermissionName')
			return array('WidevinePermissionName');
		if($baseEnumName == 'BatchJobType')
			return array('WidevineBatchJobType');		
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaFlavorParams' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new KalturaWidevineFlavorParams();
	
		if($baseClass == 'KalturaFlavorParamsOutput' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new KalturaWidevineFlavorParamsOutput();
		
		if($baseClass == 'KalturaFlavorAsset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new KalturaWidevineFlavorAsset();
			
		if($baseClass == 'assetParams' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new WidevineFlavorParams();
	
		if($baseClass == 'assetParamsOutput' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new WidevineFlavorParamsOutput();
			
		if($baseClass == 'asset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new WidevineFlavorAsset();
			
		if($baseClass == 'flavorAsset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return new WidevineFlavorAsset();
		
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::WIDEVINE)
			return new KWidevineOperationEngine($constructorArgs['params'], $constructorArgs['outFilePath']);
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == self::getApiValue(WidevineConversionEngineType::WIDEVINE))
			return new KDLOperatorWidevine($enumValue);

		if($baseClass == 'KalturaSerializer' && $enumValue == self::WIDEVINE_RESPONSE_TYPE)
			return new KalturaWidevineSerializer();
			
		if ($baseClass == 'KalturaJobData')
		{
		    if ($enumValue == WidevinePlugin::getApiValue(WidevineBatchJobType::WIDEVINE_REPOSITORY_SYNC))
			{
				return new KalturaWidevineRepositorySyncJobData();
			}
		}		
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{	
		if($baseClass == 'KalturaFlavorParams' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'KalturaWidevineFlavorParams';
	
		if($baseClass == 'KalturaFlavorParamsOutput' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'KalturaWidevineFlavorParamsOutput';
		
		if($baseClass == 'KalturaFlavorAsset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'KalturaWidevineFlavorAsset';

		if($baseClass == 'assetParams' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'WidevineFlavorParams';
	
		if($baseClass == 'assetParamsOutput' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'WidevineFlavorParamsOutput';
			
		if($baseClass == 'asset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'WidevineFlavorAsset';
			
		if($baseClass == 'flavorAsset' && $enumValue == WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR))
			return 'WidevineFlavorAsset';			
		
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::WIDEVINE)
			return 'KWidevineOperationEngine';
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == self::getApiValue(WidevineConversionEngineType::WIDEVINE))
			return 'KDLOperatorWidevine';
			
		if($baseClass == 'KalturaSerializer' && $enumValue == self::WIDEVINE_RESPONSE_TYPE)
			return 'KalturaWidevineSerializer';
		
		if ($baseClass == 'KalturaJobData')
		{
		    if ($enumValue == WidevinePlugin::getApiValue(WidevineBatchJobType::WIDEVINE_REPOSITORY_SYNC))
			{
				return 'KalturaWidevineRepositorySyncJobData';
			}
		}		
			
		return null;
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getCoreValue($type, $valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore($type, $value);
	}
	
	public static function getConversionEngineCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('conversionEngineType', $value);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getAssetTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('assetType', $value);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaTypeExtender::getExtendedTypes()
	 */
	public static function getExtendedTypes($baseClass, $enumValue) {
		$supportedBaseClasses = array(
			assetPeer::OM_CLASS,
			assetParamsPeer::OM_CLASS,
			assetParamsOutputPeer::OM_CLASS,
		);
		
		if(in_array($baseClass, $supportedBaseClasses) && $enumValue == assetType::FLAVOR)
		{
			return array(
				WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR),
			);
		}
		
		return null;		
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap() {
		$map = array(
			'widevineDrm' => 'WidevineDrmService',
		);
		return $map;	
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId) {	
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
			return false;
		return $partner->getPluginEnabled(self::PLUGIN_NAME);			
	}
	
	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::WIDEVINE_EVENTS_CONSUMER,
		);
	}
	
	public static function getWidevineAssetIdSearchData($wvAssetId)
	{
		return self::getPluginName() . $wvAssetId . self::SEARCH_DATA_SUFFIX;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSearchDataContributor::getSearchData()
	 */
	public static function getSearchData(BaseObject $object)
	{
		if($object instanceof entry)
		{
			$c = new Criteria();
			$c->add(assetPeer::ENTRY_ID, $object->getId());		
			$flavorType = self::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR);
			$c->add(assetPeer::TYPE, $flavorType);		
			$wvFlavorAssets = assetPeer::doSelect($c);
			if(count($wvFlavorAssets))
			{			
				$searchData = array();
				foreach ($wvFlavorAssets as $wvFlavorAsset) 
				{
					$searchData[] = self::getWidevineAssetIdSearchData($wvFlavorAsset->getWidevineAssetId());
				}				
				return array('plugins_data' => implode(' ', $searchData));
			}
		}
			
		return null;
	}
	
	public static function getWidevineConfigParam($key)
	{
		$widevineConfig = kConf::getMap('widevine');
		if (!is_array($widevineConfig))
		{
			KalturaLog::err('Widevine config section is not defined');
			return null;
		}

		if (!isset($widevineConfig[$key]))
		{
			KalturaLog::err('The key '.$key.' was not found in the widevine config section');
			return null;
		}

		return $widevineConfig[$key];
	}
}
