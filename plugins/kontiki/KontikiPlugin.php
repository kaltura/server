<?php
/**
 * Enable upload and playback of content to and from Kontiki ECDN
 * @package plugins.kontiki
 */
class KontikiPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader , IKalturaEventConsumers
{
	const PLUGIN_NAME = 'kontiki';
    
    const KONTIKI_ASSET_TAG = 'kontiki';
	
	const SERVICE_TOKEN_PREFIX = 'srv-';

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if ($baseClass == 'KExportEngine')
		{
			if ($enumValue == KalturaStorageProfileProtocol::KONTIKI)
			{
				list($data, $partnerId) = $constructorArgs;
				return new KKontikiExportEngine($data, $partnerId);
			}
		}
		if ($baseClass == 'kStorageExportJobData')
		{
            if ($enumValue == self::getStorageProfileProtocolCoreValue(KontikiStorageProfileProtocol::KONTIKI))
            {
                return new kKontikiStorageExportJobData();
            }
		}
        if ($baseClass == 'kStorageDeleteJobData')
        {
            if ($enumValue == self::getStorageProfileProtocolCoreValue(KontikiStorageProfileProtocol::KONTIKI))
            {
                return new kKontikiStorageDeleteJobData();
            }
        }
		if ($baseClass == 'KalturaJobData')
		{
			$jobSubType = $constructorArgs["coreJobSubType"];
			if ($jobSubType == self::getStorageProfileProtocolCoreValue(KontikiStorageProfileProtocol::KONTIKI))
			{
				if ($enumValue == BatchJobType::STORAGE_EXPORT)
				{
					return new KalturaKontikiStorageExportJobData();
				}
			 	if ($enumValue == BatchJobType::STORAGE_DELETE)
	            {
	                return new KalturaKontikiStorageDeleteJobData();
	            }
			}
        }
		
		if ($baseClass == 'KalturaStorageProfile')
        {
            if ($enumValue == self::getStorageProfileProtocolCoreValue(KontikiStorageProfileProtocol::KONTIKI))
            {
                return new KalturaKontikiStorageProfile();
            }
        }

	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue) {
		if($baseClass == 'StorageProfile' && $enumValue == self::getStorageProfileProtocolCoreValue(KontikiStorageProfileProtocol::KONTIKI))
            return 'KontikiStorageProfile';
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null) {
		if (!$baseEnumName)
		{
			return array('KontikiStorageProfileProtocol');
		}
		if ($baseEnumName == 'StorageProfileProtocol')
		{
			return array('KontikiStorageProfileProtocol');
		}

		return array();

	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
        $partner = PartnerPeer::retrieveByPK($partnerId);
        if(!$partner)
            return false;

        return $partner->getPluginEnabled(self::PLUGIN_NAME);

	}

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName() {
		return self::PLUGIN_NAME;

	}

	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	public static function getStorageProfileProtocolCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('StorageProfileProtocol', $value);
	}

	public static function getEventConsumers()
	{
        return array ('kKontikiManager');
	}
}