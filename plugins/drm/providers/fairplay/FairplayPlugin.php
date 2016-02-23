<?php
/**
 * @package plugins.fairplay
 */
class FairplayPlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaObjectLoader, IKalturaEntryContextDataContributor
{
	const PLUGIN_NAME = 'fairplay';
	const SEARCH_DATA_SUFFIX = 's';

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
		if (is_null($baseEnumName))
			return array('FairplayProviderType');
		if ($baseEnumName == 'DrmProviderType')
			return array('FairplayProviderType');
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
				return new Kaltura_Client_Fairplay_Type_FairplayProfile();
			}
			if ($baseClass == 'Form_DrmProfileConfigureExtend_SubForm' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::FAIRPLAY)
			{
				return new Form_FairplayProfileConfigureExtend_SubForm();
			}
		}

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
				return 'Kaltura_Client_Fairplay_Type_FairplayProfile';
			}
			if ($baseClass == 'Form_DrmProfileConfigureExtend_SubForm' && $enumValue == Kaltura_Client_Drm_Enum_DrmProviderType::FAIRPLAY)
			{
				return 'Form_FairplayProfileConfigureExtend_SubForm';
			}
		}
		
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
		if (in_array($partnerId, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID)))
			return true;
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
			return false;
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	public function contributeToEntryContextDataResult(entry $entry, KalturaEntryContextDataParams $contextDataParams, KalturaEntryContextDataResult $result)
	{
		if ($this->shouldContribute($entry))
		{
			$fairplayContextData = new KalturaFairplayEntryContextPluginData();

			$fairplayProfile = DrmProfilePeer::retrieveByProviderAndPartnerID(FairplayPlugin::getFairplayProviderCoreValue(), kCurrentContext::getCurrentPartnerId());
			if (!is_null($fairplayProfile))
			{
				/**
				 * @var FairplayDrmProfile $fairplayProfile
				 */
				$fairplayContextData->publicCertificate = $fairplayProfile->getPublicCertificate();
				$result->pluginData[get_class($fairplayContextData)] = $fairplayContextData;
			}
		}
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
	
}