<?php
/**
 * @package plugins.DropFolderMrss
 */
class DropFolderMrssPlugin extends KalturaPlugin implements IKalturaPlugin, IKalturaPending, IKalturaObjectLoader, IKalturaEnumerator, IKalturaAdminConsolePages
{
	const PLUGIN_NAME = 'DropFolderMrss';
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
					return new KMrssDropFolderEngine();
				}
				break;
			case ('KalturaDropFolderFile'):
				if ($enumValue == self::getDropFolderTypeCoreValue(MrssDropFolderType::MRSS) )
				{
					return new KalturaMrssDropFolderFile();
				}
				break;
			case 'Form_DropFolderConfigureExtend_SubForm':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::MRSS)
				{
					return new Form_WebexDropFolderConfigureExtend_SubForm();
				}
				break;
//			case 'Kaltura_Client_DropFolder_Type_DropFolder':
//				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::MRSS)
//				{
//					return new Kaltura_Client_WebexDropFolder_Type_WebexDropFolder();
//				}
//				break;
		}
	}
	
	public static function getObjectClass($baseClass, $enumValue)
	{
		switch ($baseClass) {
			case 'DropFolderFile':
				if ($enumValue == self::getDropFolderTypeCoreValue(MrssDropFolderType::MRSS))
				return 'MrssDropFolderFile';				
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
			return array('MrssDropFolderType', 'MrssDropFolderFileFileSyncObjectType');
		}
		if ($baseEnumName == 'DropFolderType')
		{
			return array('MrssDropFolderType');
		}
		if ($baseEnumName == 'FileSyncObjectType')
		{
			return array('MrssDropFolderFileFileSyncObjectType');
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
	
	public static function getDropFolderFileFileSyncObjectTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('FileSyncObjectType', $value);
	}
	
}
