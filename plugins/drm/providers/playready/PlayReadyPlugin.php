<?php
/**
 * @package plugins.playReady
 */
class PlayReadyPlugin extends BaseDrmPlugin implements IKalturaEnumerator, IKalturaServices , IKalturaPermissionsEnabler, IKalturaObjectLoader, IKalturaSearchDataContributor, IKalturaPending, IKalturaApplicationPartialView, IKalturaEventConsumers, IKalturaPlaybackContextDataContributor
{
	const PLUGIN_NAME = 'playReady';
	const SEARCH_DATA_SUFFIX = 's';
	const PLAY_READY_EVENTS_CONSUMER = 'kPlayReadyEventsConsumer';
	
	const ENTRY_CUSTOM_DATA_PLAY_READY_KEY_ID = 'play_ready_key_id';
	const PLAY_READY_TAG = 'playready';
	
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
			return array('PlayReadyLicenseScenario', 'PlayReadyLicenseType', 'PlayReadyProviderType', 'PlayReadySchemeName');
		if($baseEnumName == 'DrmLicenseScenario')
			return array('PlayReadyLicenseScenario');
		if($baseEnumName == 'DrmLicenseType')
			return array('PlayReadyLicenseType');
		if($baseEnumName == 'DrmProviderType')
			return array('PlayReadyProviderType');
		if ($baseEnumName == 'DrmSchemeName')
			return array('PlayReadySchemeName');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaDrmProfile' && $enumValue == PlayReadyPlugin::getPlayReadyProviderCoreValue())
			return new KalturaPlayReadyProfile();
		if($baseClass == 'KalturaDrmProfile' && $enumValue == self::getApiValue(PlayReadyProviderType::PLAY_READY))
			return new KalturaPlayReadyProfile();		
	
		if($baseClass == 'KalturaDrmPolicy' && $enumValue == PlayReadyPlugin::getPlayReadyProviderCoreValue())
			return new KalturaPlayReadyPolicy();
		
		if($baseClass == 'DrmProfile' && $enumValue == PlayReadyPlugin::getPlayReadyProviderCoreValue())
			return new PlayReadyProfile();
			
		if($baseClass == 'DrmPolicy' && $enumValue == PlayReadyPlugin::getPlayReadyProviderCoreValue())
			return new PlayReadyPolicy();
			
		if (class_exists('Kaltura_Client_Client'))
		{
			if ($baseClass == 'Kaltura_Client_Drm_Type_DrmProfile' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::PLAY_READY)
    		{
    			return new Kaltura_Client_PlayReady_Type_PlayReadyProfile();
    		}
    		if ($baseClass == 'Form_DrmProfileConfigureExtend_SubForm' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::PLAY_READY)
    		{
     			return new Form_PlayReadyProfileConfigureExtend_SubForm();
    		}	   		
    		
		}
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{	
		if($baseClass == 'KalturaDrmProfile' && $enumValue == PlayReadyPlugin::getPlayReadyProviderCoreValue())
			return 'KalturaPlayReadyProfile';
		if($baseClass == 'KalturaDrmProfile' && $enumValue == self::getApiValue(PlayReadyProviderType::PLAY_READY))
			return 'KalturaPlayReadyProfile';		
			
		if($baseClass == 'KalturaDrmPolicy' && $enumValue == PlayReadyPlugin::getPlayReadyProviderCoreValue())
			return 'KalturaPlayReadyPolicy';
		
		if($baseClass == 'DrmProfile' && $enumValue == PlayReadyPlugin::getPlayReadyProviderCoreValue())
			return 'PlayReadyProfile';
			
		if($baseClass == 'DrmPolicy' && $enumValue == PlayReadyPlugin::getPlayReadyProviderCoreValue())
			return 'PlayReadyPolicy';
			
		if (class_exists('Kaltura_Client_Client'))
		{
			if ($baseClass == 'Kaltura_Client_Drm_Type_DrmProfile' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::PLAY_READY)
    		{
    			return 'Kaltura_Client_PlayReady_Type_PlayReadyProfile';
    		}
    		if ($baseClass == 'Form_DrmProfileConfigureExtend_SubForm' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::PLAY_READY)
    		{
     			return 'Form_PlayReadyProfileConfigureExtend_SubForm';
    		}	   		
    		
		}
			
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaApplicationPartialView::getApplicationPartialViews()
	 */
	public static function getApplicationPartialViews($controller, $action)
	{
		if($controller == 'plugin' && $action == 'DrmProfileConfigureAction')
		{
			return array(
				new Kaltura_View_Helper_PlayReadyProfileConfigure(),
			);
		}
		
		return array();
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
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getPlayReadyProviderCoreValue()
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . PlayReadyProviderType::PLAY_READY;
		return kPluginableEnumsManager::apiToCore('DrmProviderType', $value);
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap() {
		$map = array(
			'playReadyDrm' => 'PlayReadyDrmService',
		);
		return $map;	
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId) {	
		if (in_array($partnerId, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID)))
			return true;		
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
			self::PLAY_READY_EVENTS_CONSUMER,
		);
	}
	
	public static function getPlayReadyKeyIdSearchData($keyId)
	{
		return self::getPluginName() . $keyId . self::SEARCH_DATA_SUFFIX;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSearchDataContributor::getSearchData()
	 */
	public static function getSearchData(BaseObject $object)
	{
		if($object instanceof entry)
		{
			$keyId = $object->getFromCustomData(self::ENTRY_CUSTOM_DATA_PLAY_READY_KEY_ID);
			if($keyId)
			{
				$searchData = self::getPlayReadyKeyIdSearchData($keyId);			
				return array('plugins_data' => $searchData);
			}
		}
			
		return null;
	}
	
	public static function getPlayReadyConfigParam($key)
	{
		return DrmPlugin::getConfigParam(self::PLUGIN_NAME, $key);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissionsEnabler::permissionEnabled()
	 */
	public static function permissionEnabled($partnerId, $permissionName) 
	{
		if($permissionName == 'PLAYREADY_PLUGIN_PERMISSION')
			kPlayReadyPartnerSetup::setupPartner($partnerId);
		
	}

    public function contributeToPlaybackContextDataResult(entry $entry, kPlaybackContextDataParams $entryPlayingDataParams, kPlaybackContextDataResult $result, kContextDataHelper $contextDataHelper)
	{
		if (self::shouldContributeToPlaybackContext($contextDataHelper->getContextDataResult()->getActions()) && $this->isSupportStreamerTypes($entryPlayingDataParams->getDeliveryProfile()->getStreamerType()) )
		{
			$playReadyProfile = DrmProfilePeer::retrieveByProviderAndPartnerID(PlayReadyPlugin::getPlayReadyProviderCoreValue(), kCurrentContext::getCurrentPartnerId());
			if ($playReadyProfile)
			{
				/* @var PlayReadyProfile $playReadyProfile */

				$signingKey = kConf::get('signing_key', 'drm', null);
				if ($signingKey)
				{
					$customDataJson = DrmLicenseUtils::createCustomDataForEntry($entry->getId(), $entryPlayingDataParams->getFlavors(), $signingKey);
					$customDataObject = reset($customDataJson);
					$data = new kDrmPlaybackPluginData();
					$data->setScheme($this->getDrmSchemeCoreValue());
					$data->setLicenseURL($this->constructUrl($playReadyProfile, self::PLUGIN_NAME, $customDataObject));
					$result->addToPluginData(self::PLUGIN_NAME, $data);
				}
			}
		}
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getDrmSchemeCoreValue()
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . PlayReadySchemeName::PLAYREADY;
		return kPluginableEnumsManager::apiToCore('DrmSchemeName', $value);
	}

	public function isSupportStreamerTypes($streamerType)
	{
		return in_array($streamerType ,array(PlaybackProtocol::SILVER_LIGHT));
	}

	public function constructUrl($playReadyProfile, $scheme, $customDataObject)
	{
		return $playReadyProfile->getLicenseServerUrl() . "/" . $scheme . "/license?custom_data=" . $customDataObject['custom_data'] . "&signature=" . $customDataObject['signature'];
	}
}

