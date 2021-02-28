<?php
/**
 * @package plugins.zoomDropFolder
 */
class ZoomDropFolderPlugin extends KalturaPlugin implements IKalturaEventConsumers, IKalturaEnumerator
{
	const PLUGIN_NAME = 'zoomDropFolder';
	
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
}