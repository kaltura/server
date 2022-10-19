<?php
/**
 * @package plugins.WebexAPIDropFolder
 */
class WebexAPIDropFolderPlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaObjectLoader, IKalturaPending, IKalturaServices
{
	const PLUGIN_NAME = 'WebexAPIDropFolder';
	const CONFIGURATION_PARAM_NAME = 'WebexAccount';
	
	public static function dependsOn()
	{
		$dropFolderDependency = new KalturaDependency(DropFolderPlugin::PLUGIN_NAME);
		$vendorDependency = new KalturaDependency(VendorPlugin::PLUGIN_NAME);
		return array($dropFolderDependency, $vendorDependency);
	}
	
	/**
	 * @return string
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function getServicesMap()
	{
		$map = array(
			'webexAPI' => 'WebexAPIService',
		);
		return $map;
	}
	
	public static function getCoreValue($type, $valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore($type, $value);
	}
	
	/**
	 * @param string|null $baseEnumName
	 * @return array
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (!$baseEnumName)
		{
			return array('WebexAPIDropFolderType');
		}
		if ($baseEnumName == 'DropFolderType')
		{
			return array('WebexAPIDropFolderType');
		}
		
		return array();
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array|null $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		switch ($baseClass)
		{
			case 'KDropFolderEngine':
				if ($enumValue == KalturaDropFolderType::WEBEX_API)
				{
					return new KWebexAPIDropFolderEngine();
				}
				break;
			case ('KalturaDropFolder'):
				if ($enumValue == self::getDropFolderTypeCoreValue(WebexAPIDropFolderType::WEBEX_API) )
				{
					return new KalturaWebexAPIDropFolder();
				}
				break;
			case ('KalturaDropFolderFile'):
				if ($enumValue == self::getDropFolderTypeCoreValue(WebexAPIDropFolderType::WEBEX_API) )
				{
					return new KalturaWebexDropFolderFile();
				}
				break;
			case 'kDropFolderContentProcessorJobData':
				if ($enumValue == self::getDropFolderTypeCoreValue(WebexAPIDropFolderType::WEBEX_API))
				{
					return new kDropFolderContentProcessorJobData();
				}
				break;
			case 'KalturaJobData':
				$jobSubType = $constructorArgs["coreJobSubType"];
				if ($enumValue == DropFolderPlugin::getApiValue(DropFolderBatchType::DROP_FOLDER_CONTENT_PROCESSOR) &&
					$jobSubType == self::getDropFolderTypeCoreValue(WebexAPIDropFolderType::WEBEX_API) )
				{
					return new KalturaDropFolderContentProcessorJobData();
				}
				break;
			case 'KalturaIntegrationSetting':
				if ($enumValue == self::getDropFolderTypeCoreValue(WebexAPIDropFolderType::WEBEX_API))
				{
					return new KalturaWebexAPIIntegrationSetting();
				}
				break;
			case 'Form_DropFolderConfigureExtend_SubForm':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::WEBEX_API)
				{
					return new Form_WebexAPIDropFolderConfigureExtend_SubForm();
				}
				break;
			case 'Kaltura_Client_DropFolder_Type_DropFolder':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::WEBEX_API)
				{
					return new Kaltura_Client_WebexAPIDropFolder_Type_WebexAPIDropFolder();
				}
				break;
		}
		return null;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'DropFolder' &&
			$enumValue == self::getDropFolderTypeCoreValue(WebexAPIDropFolderType::WEBEX_API))
		{
			return 'WebexAPIDropFolder';
		}
		else if($baseClass == 'DropFolderFile' &&
			$enumValue == self::getDropFolderTypeCoreValue(WebexAPIDropFolderType::WEBEX_API))
		{
			return 'WebexAPIDropFolderFile';
		}
		return null;
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
}