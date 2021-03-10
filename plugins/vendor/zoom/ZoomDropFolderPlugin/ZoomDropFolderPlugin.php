<?php
/**
 * @package plugins.ZoomDropFolder
 */
class ZoomDropFolderPlugin extends KalturaPlugin implements IKalturaEventConsumers, IKalturaEnumerator, IKalturaObjectLoader
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
		
		return array($dropFolderDependency);
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
		if($baseClass == 'KalturaDropFolder' && self::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM) ==
			$enumValue)
		{
			return new KalturaZoomDropFolder();
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