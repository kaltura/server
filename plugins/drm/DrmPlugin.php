<?php
/**
 * @package plugins.drm
 */
class DrmPlugin extends KalturaPlugin implements IKalturaServices, IKalturaAdminConsolePages, IKalturaPermissions, IKalturaEnumerator, IKalturaConfigurator, IKalturaObjectLoader, IKalturaEntryContextDataContributor
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
			return array('DrmPermissionName', 'DrmConversionEngineType', 'DrmAccessControlActionType');
		if($baseEnumName == 'PermissionName')
			return array('DrmPermissionName');
        if($baseEnumName == 'conversionEngineType')
            return array('DrmConversionEngineType');
        if($baseEnumName == 'RuleActionType')
            return array('DrmAccessControlActionType');

		return array();
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
        return null;
    }

    /**
     * @return string
     */
    protected static function getApiValue($value)
    {
        return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $value;
    }

    public function contributeToEntryContextDataResult($entryId, KalturaEntryContextDataParams $contextDataParams, KalturaEntryContextDataResult $result)
    {
        KalturaLog::debug("Drm contributing to context data");

        $signingKey = $this->getSigningKey();
        KalturaLog::debug("Signing key is '$signingKey'");

        $customDataJson = DrmLicenseUtils::createCustomData($entryId, $result->flavorAssets, $signingKey);

        $drmContextData = new KalturaDrmEntryContextPluginData();
        $drmContextData->flavorData = $customDataJson;
		$result->pluginData[get_class($drmContextData)] = $drmContextData;
    }

    private function getSigningKey()
    {
        $drmProfile = KalturaDrmProfile::getInstanceByType(KalturaDrmProviderType::CENC);
        $dbProfile = DrmProfilePeer::retrieveByProvider(KalturaDrmProviderType::CENC);
        $drmProfile->fromObject($dbProfile);
        $signingKey = $drmProfile->signingKey;
        return $signingKey;
    }
}


