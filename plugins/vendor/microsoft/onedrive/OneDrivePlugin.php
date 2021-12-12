<?php

/**
 * @package plugins.OneDrive
 */
class OneDrivePlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaObjectLoader, IKalturaPermissions, IKalturaApplicationTranslations
{
	const PLUGIN_NAME = 'OneDrive';

	
	/**
	 * @inheritDoc
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (!$baseEnumName)
		{
			return array('OneDriveDropFolderType', 'OneDriveVendorType');
		}

		switch ($baseEnumName)
		{
			case 'DropFolderType':
				return array('OneDriveDropFolderType');
				break;
			case 'VendorTypeEnum':
				return array('OneDriveVendorType');
				break;
		}

		return array();
	}

	/**
	 * @inheritDoc
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		switch ($baseClass) {
			case 'KDropFolderEngine':
				if ($enumValue == KalturaDropFolderType::ONE_DRIVE) {
					return new OneDriveDropFolderEngine();
				}
				break;
			case ('KalturaDropFolder'):
				if ($enumValue == self::getDropFolderTypeCoreValue(OneDriveDropFolderType::ONE_DRIVE)) {
					return new KalturaOneDriveDropFolder();
				}
				break;
			case ('KalturaDropFolderFile'):
				if ($enumValue == self::getDropFolderTypeCoreValue(OneDriveDropFolderType::ONE_DRIVE)) {
					return new KalturaMicrosoftTeamsDropFolderFile();
				}
				break;
			case 'kDropFolderContentProcessorJobData':
				if ($enumValue == self::getDropFolderTypeCoreValue(OneDriveDropFolderType::ONE_DRIVE)) {
					return new kDropFolderContentProcessorJobData();
				}
				break;
			case 'KalturaJobData':
				$jobSubType = $constructorArgs["coreJobSubType"];
				if ($enumValue == DropFolderPlugin::getApiValue(DropFolderBatchType::DROP_FOLDER_CONTENT_PROCESSOR) &&
					$jobSubType == self::getDropFolderTypeCoreValue(OneDriveDropFolderType::ONE_DRIVE)) {
					return new KalturaDropFolderContentProcessorJobData();
				}
				break;
			case 'KalturaIntegrationSetting':
				if ($enumValue == self::getVendorTypeCoreValue(OneDriveVendorType::ONE_DRIVE)) {
					return new KalturaOneDriveIntegrationSetting();
				}
				break;

			case 'Form_DropFolderConfigureExtend_SubForm':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::ONE_DRIVE)
				{
					return new Form_MicrosoftTeamsDropFolderConfigureExtend_SubForm();
				}
				break;
			case 'Kaltura_Client_DropFolder_Type_DropFolder':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::ONE_DRIVE)
				{
					return new Kaltura_Client_OneDrive_Type_OneDriveDropFolder();
				}
				break;
		}
	}

	/**
	 * @inheritDoc
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if ($baseClass == 'VendorIntegration' &&
			$enumValue == self::getVendorTypeCoreValue(OneDriveVendorType::ONE_DRIVE))
		{
			return 'OneDriveIntegration';
		}

		if($baseClass == 'DropFolder' &&
			$enumValue == self::getDropFolderTypeCoreValue(OneDriveDropFolderType::ONE_DRIVE))
		{
			return 'OneDriveDropFolder';
		}

		if($baseClass == 'DropFolderFile' &&
			$enumValue == self::getDropFolderTypeCoreValue(OneDriveDropFolderType::ONE_DRIVE))
		{
			return 'MicrosoftTeamsDropFolderFile';
		}

		return null;
	}

	/**
	 * @inheritDoc
	 */
	public static function dependsOn()
	{
		$dropFolderDependency = new KalturaDependency(DropFolderPlugin::PLUGIN_NAME);
		$vendorDependency = new KalturaDependency(VendorPlugin::PLUGIN_NAME);
		$microsoftTeamsDropFolderDependency = new KalturaDependency(MicrosoftTeamsDropFolderPlugin::getPluginName());

		return array($dropFolderDependency, $vendorDependency, $microsoftTeamsDropFolderDependency);
	}

	/**
	 * @inheritDoc
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/**
	 * @param $valueName
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	public static function getDropFolderTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('DropFolderType', $value);
	}

	public static function getVendorTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('VendorTypeEnum', $value);
	}

	/**
	 * @inheritDoc
	 */
	public static function isAllowedPartner($partnerId)
	{
		if (in_array($partnerId, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID)))
			return true;

		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	/**
	 * @inheritDoc
	 */
	public static function getTranslations($locale)
	{
		$array = array();
		
		$langFilePath = __DIR__ . "/config/lang/$locale.php";
		if (!file_exists($langFilePath))
		{
			$default = 'en';
			$langFilePath = __DIR__ . "/config/lang/$default.php";
		}
		
		KalturaLog::info("Loading file [$langFilePath]");
		$array = include($langFilePath);
		
		return array($locale => $array);
	}
}
