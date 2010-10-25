<?php

class QuickTimeToolsPlugin extends KalturaPlugin implements KalturaObjectLoaderPlugin
{
	const PLUGIN_NAME = 'quickTimeTools';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($objectType, $enumValue, array $constructorArgs = null)
	{
		if($objectType == KalturaPluginManager::OBJECT_TYPE_OPERATION_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_QUICK_TIME_PLAYER_TOOLS)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
				
			$params = $constructorArgs['params'];
			return new KOperationEngineQtTools($params->qtToolsCmd, $constructorArgs['outFilePath']);
		}
			
		if($objectType == KalturaPluginManager::OBJECT_TYPE_KDL_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_QUICK_TIME_PLAYER_TOOLS)
		{
			return new KDLTranscoderQTPTools($enumValue);
		}
		
		return null;
	}
	
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($objectType, $enumValue)
	{
		if($objectType == KalturaPluginManager::OBJECT_TYPE_OPERATION_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_QUICK_TIME_PLAYER_TOOLS)
			return 'KOperationEngineQtTools';
			
		if($objectType == KalturaPluginManager::OBJECT_TYPE_KDL_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_QUICK_TIME_PLAYER_TOOLS)
			return 'KDLTranscoderQTPTools';
		
		return null;	
	}
}
