<?php
/**
 * @package plugins.drm
 */
class DrmPlugin extends KalturaPlugin implements IKalturaServices, IKalturaAdminConsolePages, IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader, IKalturaEntryContextDataContributor,IKalturaPermissionsEnabler, IKalturaPlaybackContextDataContributor
{
	const PLUGIN_NAME = 'drm';

	/* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap() {
		$map = array(
			'drmPolicy' => 'DrmPolicyService',
			'drmProfile' => 'DrmProfileService',
            'drmLicenseAccess' => 'DrmLicenseAccessService'
		);
		return $map;	
	}

	/* (non-PHPdoc)
	 * @see IKalturaAdminConsolePages::getApplicationPages()
	 */
	public static function getApplicationPages()
	{
		$pages = array();
		$pages[] = new DrmProfileListAction();
		$pages[] = new DrmProfileConfigureAction();
		$pages[] = new DrmProfileDeleteAction();

		return $pages;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId) {	
		
		if ($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
			return false;
		return $partner->getPluginEnabled(self::PLUGIN_NAME);			
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('DrmPermissionName', 'DrmConversionEngineType', 'DrmAccessControlActionType', 'CencSchemeName' );
		if($baseEnumName == 'PermissionName')
			return array('DrmPermissionName');
        if($baseEnumName == 'conversionEngineType')
            return array('DrmConversionEngineType');
        if($baseEnumName == 'RuleActionType')
            return array('DrmAccessControlActionType');
		if ($baseEnumName == 'DrmSchemeName')
			return array('CencSchemeName');
		return array();
	}

	public static function getConfigParam($configName, $key)
	{
		$config = kConf::getMap($configName);
		if (!is_array($config))
		{
			KalturaLog::err($configName.' config section is not defined');
			return null;
		}

		if (!isset($config[$key]))
		{
			KalturaLog::err('The key '.$key.' was not found in the '.$configName.' config section');
			return null;
		}

		return $config[$key];
	}

    /* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
    public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
    {
        if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::CENC)
            return new KCEncOperationEngine($constructorArgs['params'], $constructorArgs['outFilePath']);
        if($baseClass == 'KDLOperatorBase' && $enumValue == self::getApiValue(DrmConversionEngineType::CENC))
            return new KDLOperatorDrm($enumValue);
        if ($baseClass == 'Kaltura_Client_Drm_Type_DrmProfile' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::CENC)
            return new Kaltura_Client_Drm_Type_DrmProfile();
        if($baseClass == 'kRuleAction' && $enumValue == DrmAccessControlActionType::DRM_POLICY)
            return new kAccessControlDrmPolicyAction();
        if($baseClass == 'KalturaRuleAction' && $enumValue == DrmAccessControlActionType::DRM_POLICY)
            return new KalturaAccessControlDrmPolicyAction();
	    if ($baseClass == 'KalturaPluginData' && $enumValue == self::getPluginName())
		    return new KalturaDrmEntryContextPluginData();
	    if ($baseClass == 'KalturaDrmPlaybackPluginData' && $enumValue == 'kDrmPlaybackPluginData')
		    return new KalturaDrmPlaybackPluginData();
        return null;
    }

    /* (non-PHPdoc)
    * @see IKalturaObjectLoader::getObjectClass()
     */
    public static function getObjectClass($baseClass, $enumValue)
    {
        if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::CENC)
            return "KDRMOperationEngine";
        if($baseClass == 'KDLOperatorBase' && $enumValue == self::getApiValue(DrmConversionEngineType::CENC))
            return "KDLOperatorrm";
        if($baseClass == 'KalturaDrmProfile' && $enumValue == KalturaDrmProviderType::CENC)
            return "KalturaDrmProfile";
        if($baseClass == 'DrmProfile' && $enumValue == KalturaDrmProviderType::CENC)
            return "DrmProfile";
        if ($baseClass == 'Kaltura_Client_Drm_Type_DrmProfile' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::CENC)
            return 'Kaltura_Client_Drm_Type_DrmProfile';
        if($baseClass == 'kRuleAction' && $enumValue == DrmAccessControlActionType::DRM_POLICY)
            return 'kAccessControlDrmPolicyAction';
        if($baseClass == 'KalturaRuleAction' && $enumValue == DrmAccessControlActionType::DRM_POLICY)
            return 'KalturaAccessControlDrmPolicyAction';
	    if ($baseClass == 'KalturaPluginData' && $enumValue == self::getPluginName())
		    return 'KalturaDrmEntryContextPluginData';
        return null;
    }

    /**
     * @return string
     */
    protected static function getApiValue($value)
    {
        return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $value;
    }

    public function contributeToEntryContextDataResult(entry $entry, accessControlScope $contextDataParams, kContextDataHelper $contextDataHelper)
    {
	    if ($this->shouldContribute($entry ))
	    {
		    $signingKey = $this->getSigningKey();
		    if (!is_null($signingKey))
		    {
			    KalturaLog::info("Signing key is '$signingKey'");
			    $customDataJson = DrmLicenseUtils::createCustomData($entry->getId(), $contextDataHelper->getAllowedFlavorAssets(), $signingKey);
			    $drmContextData = new kDrmEntryContextPluginData();
			    $drmContextData->setFlavorData($customDataJson);
			    return $drmContextData;
		    }
	    }
	    return null;
    }

	public function contributeToPlaybackContextDataResult(entry $entry, kPlaybackContextDataParams $entryPlayingDataParams, kPlaybackContextDataResult $result, kContextDataHelper $contextDataHelper)
	{
		if ( $this->shouldContributeToPlaybackContext($contextDataHelper->getContextDataResult()->getActions()) && $this->isSupportStreamerTypes($entryPlayingDataParams->getDeliveryProfile()->getStreamerType()))
		{
			$dbProfile = DrmProfilePeer::retrieveByProviderAndPartnerID(KalturaDrmProviderType::CENC, kCurrentContext::getCurrentPartnerId());
			if ($dbProfile)
			{
				$signingKey = $dbProfile->getSigningKey();
				if ($signingKey)
				{
					$customDataJson = DrmLicenseUtils::createCustomDataForEntry($entry->getId(), $entryPlayingDataParams->getFlavors(), $signingKey);
					$customDataObject = reset($customDataJson);

					foreach (CencSchemeName::getAdditionalValues() as $scheme)
					{
						$data = new kDrmPlaybackPluginData();
						$data->setLicenseURL($this->constructUrl($dbProfile, $this->getUrlName($scheme), $customDataObject));
						$data->setScheme($this->getDrmSchemeCoreValue($scheme));
						$result->addToPluginData($scheme, $data);
					}
				}
			}
		}
	}

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public function getUrlName($scheme)
	{
		switch ($scheme)
		{
			case CencSchemeName::PLAYREADY_CENC:
				return 'cenc/playready';
			case CencSchemeName::WIDEVINE_CENC:
				return 'cenc/widevine';
			default:
				return '';
		}
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getDrmSchemeCoreValue($scheme)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $scheme;
		return kPluginableEnumsManager::apiToCore('DrmSchemeName', $value);
	}

	public function isSupportStreamerTypes($streamerType)
	{
		return in_array($streamerType ,array(PlaybackProtocol::MPEG_DASH));
	}

	public function constructUrl($dbProfile, $scheme, $customDataObject)
	{
		return $dbProfile->getLicenseServerUrl() . "/" . $scheme . "/license?custom_data=" . $customDataObject['custom_data'] . "&signature=" . $customDataObject['signature'];
	}

    private function getSigningKey()
    {
	    $dbProfile = DrmProfilePeer::retrieveByProviderAndPartnerID(KalturaDrmProviderType::CENC, kCurrentContext::getCurrentPartnerId());
	    if (!is_null($dbProfile))
	    {
		    $signingKey = $dbProfile->getSigningKey();
		    return $signingKey;
	    }
	    return null;
    }

	/**
	 * @param array<kRuleAction> $actions
	 * @return bool
	 */
    protected function shouldContributeToPlaybackContext(array $actions)
    {
	    foreach ($actions as $action)
	    {
		    /*** @var kRuleAction $action */
		    if ($action->getType() == DrmAccessControlActionType::DRM_POLICY)
			    return true;
	    }

	    return false;
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

	/* (non-PHPdoc)
	 * @see IKalturaPermissionsEnabler::permissionEnabled()
	 */
	public static function permissionEnabled($partnerId, $permissionName)
	{
		if ($permissionName == 'DRM_PLUGIN_PERMISSION')
		{
			kDrmPartnerSetup::setupPartner($partnerId);
		}
	}

}


