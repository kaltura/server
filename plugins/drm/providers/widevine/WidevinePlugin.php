<?php
/**
 * @package plugins.widevine
 */
class WidevinePlugin extends BaseDrmPlugin implements IKalturaEnumerator, IKalturaServices , IKalturaPermissions, IKalturaObjectLoader, IKalturaEventConsumers, IKalturaTypeExtender, IKalturaSearchDataContributor, IKalturaPending, IKalturaPlaybackContextDataContributor
{
	const PLUGIN_NAME = 'widevine';
	const WIDEVINE_EVENTS_CONSUMER = 'kWidevineEventsConsumer';
	const WIDEVINE_RESPONSE_TYPE = 'widevine';
	const WIDEVINE_ENABLE_DISTRIBUTION_DATES_SYNC_PERMISSION = 'WIDEVINE_ENABLE_DISTRIBUTION_DATES_SYNC';
	const SEARCH_DATA_SUFFIX = 's';
	
	const REGISTER_ASSET_URL_PART = '/registerasset/';
	const GET_ASSET_URL_PART = '/getasset/';
	
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
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$drmDependency = new KalturaDependency(DrmPlugin::getPluginName());
		
		return array($drmDependency);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{	
		if(is_null($baseEnumName))
			return array('WidevineConversionEngineType', 'WidevineAssetType', 'WidevinePermissionName', 'WidevineBatchJobType', 'WidevineProviderType', 'WidevineSchemeName');
		if($baseEnumName == 'conversionEngineType')
			return array('WidevineConversionEngineType');
		if($baseEnumName == 'assetType')
			return array('WidevineAssetType');
		if($baseEnumName == 'PermissionName')
			return array('WidevinePermissionName');
		if($baseEnumName == 'BatchJobType')
			return array('WidevineBatchJobType');		
		if($baseEnumName == 'DrmProviderType')
			return array('WidevineProviderType');
		if ($baseEnumName == 'DrmSchemeName')
			return array('WidevineSchemeName');
			
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
		if($baseClass == 'KalturaDrmProfile' && $enumValue == WidevinePlugin::getWidevineProviderCoreValue())
			return new KalturaWidevineProfile();
		if($baseClass == 'KalturaDrmProfile' && $enumValue == self::getApiValue(WidevineProviderType::WIDEVINE))
			return new KalturaWidevineProfile();

		if($baseClass == 'DrmProfile' && $enumValue == WidevinePlugin::getWidevineProviderCoreValue())
			return new WidevineProfile();

		if (class_exists('Kaltura_Client_Client'))
		{
			if ($baseClass == 'Kaltura_Client_Drm_Type_DrmProfile' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::WIDEVINE)
    		{
    			return new Kaltura_Client_Widevine_Type_WidevineProfile();
    		}
    		if ($baseClass == 'Form_DrmProfileConfigureExtend_SubForm' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::WIDEVINE)
    		{
     			return new Form_WidevineProfileConfigureExtend_SubForm();
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
		if($baseClass == 'KalturaDrmProfile' && $enumValue == WidevinePlugin::getWidevineProviderCoreValue())
			return 'KalturaWidevineProfile';
		if($baseClass == 'KalturaDrmProfile' && $enumValue == self::getApiValue(WidevineProviderType::WIDEVINE))
			return 'KalturaWidevineProfile';

		if($baseClass == 'DrmProfile' && $enumValue == WidevinePlugin::getWidevineProviderCoreValue())
			return 'WidevineProfile';

		if (class_exists('Kaltura_Client_Client'))
		{
			if ($baseClass == 'Kaltura_Client_Drm_Type_DrmProfile' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::WIDEVINE)
    		{
    			return 'Kaltura_Client_Widevine_Type_WidevineProfile';
    		}

    		if ($baseClass == 'Form_DrmProfileConfigureExtend_SubForm' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::WIDEVINE)
    		{
     			return 'Form_WidevineProfileConfigureExtend_SubForm';
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
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getWidevineProviderCoreValue()
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . WidevineProviderType::WIDEVINE;
		return kPluginableEnumsManager::apiToCore('DrmProviderType', $value);
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
		return DrmPlugin::getConfigParam(self::PLUGIN_NAME, $key);
	}

	public function contributeToPlaybackContextDataResult(entry $entry, kPlaybackContextDataParams $entryPlayingDataParams, kPlaybackContextDataResult $result, kContextDataHelper $contextDataHelper)
	{
		if (self::shouldContributeToPlaybackContext($contextDataHelper->getContextDataResult()->getActions()) && $this->isSupportStreamerTypes($entryPlayingDataParams->getDeliveryProfile()->getStreamerType()))
		{
			foreach ($entryPlayingDataParams->getFlavors() as $flavor)
			{
					if ( !in_array("widevine",explode(",",$flavor->getTags())))
						$result->addToFlavorIdsToRemove($flavor->getId());
			}

			$widevineProfile = DrmProfilePeer::retrieveByProviderAndPartnerID(WidevinePlugin::getWidevineProviderCoreValue(), kCurrentContext::getCurrentPartnerId());
			if (!$widevineProfile)
			{
				$widevineProfile = new WidevineProfile();
				$widevineProfile->setName('default');
			}

			if ($widevineProfile)
			{
				/* @var WidevineProfile $widevineProfile */

				$signingKey = kConf::get('signing_key', 'drm', null);
				if ($signingKey)
				{
					$customDataJson = DrmLicenseUtils::createCustomDataForEntry($entry->getId(), $entryPlayingDataParams->getFlavors(), $signingKey);
					$customDataObject = reset($customDataJson);
					$data = new kDrmPlaybackPluginData();
					$data->setLicenseURL($this->constructUrl($widevineProfile, self::getPluginName(), $customDataObject));
					$data->setScheme($this->getDrmSchemeCoreValue());
					$result->addToPluginData(self::getPluginName(), $data);
				}
			}
		}
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getDrmSchemeCoreValue()
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . WidevineSchemeName::WIDEVINE;
		return kPluginableEnumsManager::apiToCore('DrmSchemeName', $value);
	}


	public function isSupportStreamerTypes($streamerType)
	{
		return in_array($streamerType , array(PlaybackProtocol::HTTP));
	}

	public function constructUrl($widevineProfile, $scheme, $customDataObject)
	{
		return $widevineProfile->getLicenseServerUrl() . "/" . $scheme . "/license?custom_data=" . $customDataObject['custom_data'] . "&signature=" . $customDataObject['signature'];
	}

}
