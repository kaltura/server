<?php
/**
 * @package plugins.fairplay
 */
class FairplayPlugin extends BaseDrmPlugin implements IKalturaEnumerator, IKalturaObjectLoader, IKalturaEntryContextDataContributor, IKalturaPending, IKalturaPlayManifestContributor, IKalturaPlaybackContextDataContributor
{
	const PLUGIN_NAME = 'fairplay';
	const URL_NAME = 'fps';
	const SEARCH_DATA_SUFFIX = 's';

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getUrlName()
	{
		return self::URL_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (is_null($baseEnumName))
			return array('FairplayProviderType', 'FairplaySchemeName');
		if ($baseEnumName == 'DrmProviderType')
			return array('FairplayProviderType');
		if ($baseEnumName == 'DrmSchemeName')
			return array('FairplaySchemeName');
		return array();
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if ($baseClass == 'KalturaDrmProfile' && $enumValue == FairplayPlugin::getFairplayProviderCoreValue() )
			return new KalturaFairplayDrmProfile();
		if ($baseClass == 'DrmProfile' && $enumValue ==  FairplayPlugin::getFairplayProviderCoreValue())
			return new FairplayDrmProfile();

		if (class_exists('Kaltura_Client_Client'))
		{
			if ($baseClass == 'Kaltura_Client_Drm_Type_DrmProfile' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::FAIRPLAY)
			{
				return new Kaltura_Client_Fairplay_Type_FairplayDrmProfile();
			}
			if ($baseClass == 'Form_DrmProfileConfigureExtend_SubForm' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::FAIRPLAY)
			{
				return new Form_FairplayProfileConfigureExtend_SubForm();
			}
		}
		if ($baseClass == 'KalturaPluginData' && $enumValue == self::getPluginName())
			return new KalturaFairplayEntryContextPluginData();
		if ($baseClass == 'KalturaDrmPlaybackPluginData' && $enumValue == 'kFairPlayPlaybackPluginData')
			return new KalturaFairPlayPlaybackPluginData();
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if ($baseClass == 'KalturaDrmProfile' && $enumValue == FairplayPlugin::getFairplayProviderCoreValue() )
			return 'KalturaFairplayDrmProfile';
		if ($baseClass == 'DrmProfile' && $enumValue ==  FairplayPlugin::getFairplayProviderCoreValue())
			return 'FairplayDrmProfile';

		if (class_exists('Kaltura_Client_Client'))
		{
			if ($baseClass == 'Kaltura_Client_Drm_Type_DrmProfile' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::FAIRPLAY)
			{
				return 'Kaltura_Client_Fairplay_Type_FairplayDrmProfile';
			}
			if ($baseClass == 'Form_DrmProfileConfigureExtend_SubForm' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::FAIRPLAY)
			{
				return 'Form_FairplayProfileConfigureExtend_SubForm';
			}
		}
		if ($baseClass == 'KalturaPluginData' && $enumValue == self::getPluginName())
			return 'KalturaFairplayEntryContextPluginData';
		return null;
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getFairplayProviderCoreValue()
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . FairplayProviderType::FAIRPLAY;
		return kPluginableEnumsManager::apiToCore('DrmProviderType', $value);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId) {
		return DrmPlugin::isAllowedPartner($partnerId);
	}

	public function contributeToEntryContextDataResult(entry $entry, accessControlScope $contextDataParams, kContextDataHelper $contextDataHelper)
	{
		if ($this->shouldContribute($entry))
		{
			$fairplayContextData = new kFairplayEntryContextPluginData();
			$fairplayProfile = DrmProfilePeer::retrieveByProviderAndPartnerID(FairplayPlugin::getFairplayProviderCoreValue(), kCurrentContext::getCurrentPartnerId());
			if (!is_null($fairplayProfile))
			{
				/**
				 * @var FairplayDrmProfile $fairplayProfile
				 */
				$fairplayContextData->publicCertificate = $fairplayProfile->getPublicCertificate();
				return $fairplayContextData;
			}
		}
		return null;
	}

	/**
	 * @param entry $entry
	 * @return bool
	 */
	protected function shouldContribute(entry $entry)
	{
		if ($entry->getAccessControl())
		{
			foreach ($entry->getAccessControl()->getRulesArray() as $rule)
			{
				/**
				 * @var kRule $rule
				 */
				foreach ($rule->getActions() as $action)
				{
					/**
					 * @var kRuleAction $action
					 */
					if ($action->getType() == DrmAccessControlActionType::DRM_POLICY)
					{
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Returns a Kaltura dependency object that defines the relationship between two plugins.
	 *
	 * @return array<KalturaDependency> The Kaltura dependency object
	 */
	public static function dependsOn()
	{
		$drmDependency = new KalturaDependency(DrmPlugin::getPluginName());

		return array($drmDependency);
	}

	public static function getManifestEditors($config)
	{
		$contributors = array();
		if (self::shouldEditManifest($config))
		{
			$contributor = new FairplayManifestEditor();
			$contributor->entryId = $config->entryId;
			$contributors[] = $contributor;
		}
		return $contributors;
	}

	private static function shouldEditManifest($config)
	{
		if($config->rendererClass == 'kM3U8ManifestRenderer' 
			&& in_array($config->deliveryProfile->getType(), array(DeliveryProfileType::VOD_PACKAGER_HLS, DeliveryProfileType::VOD_PACKAGER_HLS_DIRECT)) 
			&& $config->deliveryProfile->getAllowFairplayOffline())
			return true;

		return false;
	}

    public function contributeToPlaybackContextDataResult(entry $entry, kPlaybackContextDataParams $entryPlayingDataParams, kPlaybackContextDataResult $result, kContextDataHelper $contextDataHelper)
	{
		if (self::shouldContributeToPlaybackContext($contextDataHelper->getContextDataResult()->getActions()) && $this->isSupportStreamerTypes($entryPlayingDataParams->getDeliveryProfile()->getStreamerType()))
		{
			$fairplayProfile = DrmProfilePeer::retrieveByProviderAndPartnerID(FairplayPlugin::getFairplayProviderCoreValue(), kCurrentContext::getCurrentPartnerId());
			if ($fairplayProfile)
			{
				/* @var FairplayDrmProfile $fairplayProfile */

				$signingKey = kConf::get('signing_key', 'drm', null);
				if ($signingKey)
				{
					$customDataJson = DrmLicenseUtils::createCustomDataForEntry($entry->getId(), $entryPlayingDataParams->getFlavors(), $signingKey);
					$customDataObject = reset($customDataJson);
					$data = new kFairPlayPlaybackPluginData();
					$data->setLicenseURL($this->constructUrl($fairplayProfile, self::getUrlName(), $customDataObject));
					$data->setScheme($this->getDrmSchemeCoreValue());
					$data->setCertificate($fairplayProfile->getPublicCertificate());
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
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . FairplaySchemeName::FAIRPLAY;
		return kPluginableEnumsManager::apiToCore('DrmSchemeName', $value);
	}


	public function isSupportStreamerTypes($streamerType)
	{
		return in_array($streamerType ,array(PlaybackProtocol::APPLE_HTTP));
	}

	public function constructUrl($fairplayProfile, $scheme, $customDataObject)
	{
		return $fairplayProfile->getLicenseServerUrl() . "/" . $scheme . "/license?custom_data=" . $customDataObject['custom_data'] . "&signature=" . $customDataObject['signature'];
	}

}