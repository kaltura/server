<?php
/**
 * @package plugins.dropFolderMRSS
 */
class DropFolderMRSSPlugin extends KalturaPlugin implements IKalturaPlugin, IKalturaPending, IKalturaPermissions, IKalturaObjectLoader, IKalturaEnumerator, IKalturaAdminConsolePages
{
	const PLUGIN_NAME = 'dropFolderMRSS';
	const DROP_FOLDER_PLUGIN_NAME = 'dropFolder';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName() {
		return self::PLUGIN_NAME;
	}

	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		switch ($baseClass)
		{
			case 'KDropFolderEngine':
				if ($enumValue == KalturaDropFolderType::MRSS)
				{
					return new KMRSSDropFolderEngine();
				}
				break;
			case ('KalturaDropFolder'):
				if ($enumValue == self::getDropFolderTypeCoreValue(MRSSDropFolderType::MRSS) )
				{
					return new KalturaMRSSDropFolder();
				}
				break;
			case ('KalturaDropFolderFile'):
				if ($enumValue == self::getDropFolderTypeCoreValue(MRSSDropFolderType::MRSS) )
				{
					return new KalturaMRSSDropFolderFile();
				}
				break;
			case 'kDropFolderContentProcessorJobData':
				if ($enumValue == self::getDropFolderTypeCoreValue(MRSSDropFolderType::MRSS))
				{
					return new kMRSSDropFolderContentProcessorJobData();
				}
				break;
			case 'KalturaJobData':
				$jobSubType = $constructorArgs["coreJobSubType"];
			    if ($enumValue == DropFolderPlugin::getApiValue(DropFolderBatchType::DROP_FOLDER_CONTENT_PROCESSOR) &&
					$jobSubType == self::getDropFolderTypeCoreValue(MRSSDropFolderType::MRSS) )
				{
					return new KalturaMRSSDropFolderContentProcessorJobData();
				}
				break;
			case 'Form_DropFolderConfigureExtend_SubForm':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::MRSS)
				{
					return new Form_WebexDropFolderConfigureExtend_SubForm();
				}
				break;
			case 'Kaltura_Client_DropFolder_Type_DropFolder':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::MRSS)
				{
					return new Kaltura_Client_WebexDropFolder_Type_WebexDropFolder();
				}
				break;
				break;
				
		}
	}
	
	public static function getObjectClass($baseClass, $enumValue)
	{
		switch ($baseClass) {
			case 'DropFolder':
				if ($enumValue == self::getDropFolderTypeCoreValue(MRSSDropFolderType::MRSS))
				return 'MRSSDropFolder';				
				break;
			case 'DropFolderFile':
				if ($enumValue == self::getDropFolderTypeCoreValue(MRSSDropFolderType::MRSS))
				return 'MRSSDropFolderFile';				
				break;

		}
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (!$baseEnumName)
		{
			return array('MRSSDropFolderType');
		}
		if ($baseEnumName == 'DropFolderType')
		{
			return array('MRSSDropFolderType');
		}

		return array();
	}
		
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn() {
		$dropFolderDependency = new KalturaDependency(self::DROP_FOLDER_PLUGIN_NAME);
		
		return array($dropFolderDependency);
	}

	/* (non-PHPdoc)
	 * @see IKalturaApplicationPages::getApplicationPages()
	 */
	public static function getApplicationPages() {
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId) {
		if (in_array($partnerId, array(Partner::ADMIN_CONSOLE_PARTNER_ID, Partner::BATCH_PARTNER_ID)))
			return true;
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	/**
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
	
}
