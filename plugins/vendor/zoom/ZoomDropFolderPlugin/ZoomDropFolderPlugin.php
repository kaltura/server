<?php
/**
 * @package plugins.ZoomDropFolder
 */
class ZoomDropFolderPlugin extends KalturaPlugin implements IKalturaEventConsumers, IKalturaEnumerator, IKalturaObjectLoader, IKalturaPending
{
	const PLUGIN_NAME = 'zoomDropFolder';
	const DROP_FOLDER_PLUGIN_NAME = 'dropFolder';
	const EVENT_ZOOM_DROP_FOLDER_FLOW_MANAGER = 'kZoomDropFolderFlowManager';
	
	
	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::EVENT_ZOOM_DROP_FOLDER_FLOW_MANAGER
		);
	}
	
	public static function dependsOn()
	{
		$dropFolderDependency = new KalturaDependency(self::DROP_FOLDER_PLUGIN_NAME);
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
			return array('ZoomDropFolderType');
		}
		if ($baseEnumName == 'DropFolderType')
		{
			return array('ZoomDropFolderType');
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
				if ($enumValue == KalturaDropFolderType::ZOOM)
				{
					return new KZoomDropFolderEngine();
				}
				break;
			case ('KalturaDropFolder'):
				if ($enumValue == self::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM) )
				{
					return new KalturaZoomDropFolder();
				}
				break;
			case ('KalturaDropFolderFile'):
				if ($enumValue == self::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM) )
				{
					return new KalturaZoomDropFolderFile();
				}
				break;
			case 'kDropFolderContentProcessorJobData':
				if ($enumValue == self::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM))
				{
					return new kDropFolderContentProcessorJobData();
				}
				break;
			case 'KalturaJobData':
				$jobSubType = $constructorArgs["coreJobSubType"];
				if ($enumValue == DropFolderPlugin::getApiValue(DropFolderBatchType::DROP_FOLDER_CONTENT_PROCESSOR) &&
					$jobSubType == self::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM) )
				{
					return new KalturaDropFolderContentProcessorJobData();
				}
				break;
			case 'Kaltura_Client_DropFolder_Type_DropFolder':
				if ($enumValue == Kaltura_Client_DropFolder_Enum_DropFolderType::ZOOM)
				{
					return new Kaltura_Client_ZoomDropFolder_Type_ZoomDropFolder();
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
			$enumValue == self::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM))
		{
			return 'ZoomDropFolder';
		}
		else if($baseClass == 'DropFolderFile' &&
			$enumValue == self::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM))
		{
			return 'ZoomDropFolderFile';
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