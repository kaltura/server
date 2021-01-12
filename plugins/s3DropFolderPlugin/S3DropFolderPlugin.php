<?php
	/**
	 * @package plugins.S3DropFolder
	 */
class S3DropFolderPlugin extends KalturaPlugin implements IKalturaPending, IKalturaObjectLoader, IKalturaEnumerator
{
	const PLUGIN_NAME = 'S3DropFolder';
	const DROP_FOLDER_PLUGIN_NAME = 'dropFolder';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	public static function dependsOn()
	{
		$dropFolderDependency = new KalturaDependency(self::DROP_FOLDER_PLUGIN_NAME);

		return array($dropFolderDependency);
	}

	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		switch ($baseClass)
		{
			case 'KDropFolderEngine':
				if ($enumValue == KalturaDropFolderType::S3DROPFOLDER)
				{
					return new KS3DropFolderEngine();
				}
				break;
			case ('KalturaDropFolder'):
				if ($enumValue == self::getDropFolderTypeCoreValue(S3DropFolderType::S3DROPFOLDER) )
				{
					return new KalturaS3DropFolder();
				}
				break;
			case ('KalturaDropFolderFile'):
				if ($enumValue == self::getDropFolderTypeCoreValue(S3DropFolderType::S3DROPFOLDER) )
				{
					return new KalturaS3DropFolderFile();
				}
				break;
			case 'kDropFolderContentProcessorJobData':
				if ($enumValue == self::getDropFolderTypeCoreValue(S3DropFolderType::S3DROPFOLDER))
				{
					return new kDropFolderContentProcessorJobData();
				}
				break;
			case 'KalturaJobData':
				$jobSubType = $constructorArgs["coreJobSubType"];
				if ($enumValue == DropFolderPlugin::getApiValue(DropFolderBatchType::DROP_FOLDER_CONTENT_PROCESSOR) &&
					$jobSubType == self::getDropFolderTypeCoreValue(S3DropFolderType::S3DROPFOLDER) )
				{
					return new KalturaDropFolderContentProcessorJobData();
				}
				break;
			case 'Form_DropFolderConfigureExtend_SubForm':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::S3DROPFOLDER)
				{
					return new Form_S3DropFolderConfigureExtend_SubForm();
				}
				break;
			case 'Kaltura_Client_DropFolder_Type_DropFolder':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::S3DROPFOLDER)
				{
					return new Kaltura_Client_S3DropFolder_Type_S3DropFolder();
				}
				break;
			case 'kDropFolderXmlFileHandler':
				if ($enumValue == self::getDropFolderTypeCoreValue(S3DropFolderType::S3DROPFOLDER))
				{
					return new kDropFolderXmlFileHandler();
				}
				break;
		}
		return null;
	}

	public static function getObjectClass($baseClass, $enumValue)
	{
		switch ($baseClass)
		{
			case 'DropFolder':
				if ($enumValue == self::getDropFolderTypeCoreValue(S3DropFolderType::S3DROPFOLDER))
				{
					return 'S3DropFolder';
				}
				break;
			case 'DropFolderFile':
				if ($enumValue == self::getDropFolderTypeCoreValue(S3DropFolderType::S3DROPFOLDER))
				{
					return 'S3DropFolderFile';
				}
				break;
		}
	}

	public static function getEnums($baseEnumName = null)
	{
		if (!$baseEnumName || $baseEnumName == 'DropFolderType')
		{
			return array('S3DropFolderType');
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

	public static function getDropFolderTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('DropFolderType', $value);
	}
}
