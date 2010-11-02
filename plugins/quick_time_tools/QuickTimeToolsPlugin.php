<?php

class QuickTimeToolsPlugin extends KalturaPlugin implements IKalturaObjectLoader
{
	const PLUGIN_NAME = 'quickTimeTools';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::QUICK_TIME_PLAYER_TOOLS)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
				
			$params = $constructorArgs['params'];
			return new KOperationEngineQtTools($params->qtToolsCmd, $constructorArgs['outFilePath']);
		}
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == kConvertJobData::CONVERSION_ENGINE_QUICK_TIME_PLAYER_TOOLS)
		{
			return new KDLTranscoderQTPTools($enumValue);
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
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::QUICK_TIME_PLAYER_TOOLS)
			return 'KOperationEngineQtTools';
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == kConvertJobData::CONVERSION_ENGINE_QUICK_TIME_PLAYER_TOOLS)
			return 'KDLTranscoderQTPTools';
		
		return null;	
	}
}
