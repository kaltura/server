<?php

class QuickTimeToolsPlugin extends KalturaPlugin implements IKalturaObjectLoader, IKalturaEnumerator
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
		if($baseClass == 'KOperationEngine' && $enumValue == QuickTimeToolsConversionEngineType::get()->apiValue(QuickTimeToolsConversionEngineType::QUICK_TIME_PLAYER_TOOLS))
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
				
			$params = $constructorArgs['params'];
			return new KOperationEngineQtTools($params->qtToolsCmd, $constructorArgs['outFilePath']);
		}
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == QuickTimeToolsConversionEngineType::get()->coreValue(QuickTimeToolsConversionEngineType::QUICK_TIME_PLAYER_TOOLS))
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
		if($baseClass == 'KOperationEngine' && $enumValue == QuickTimeToolsConversionEngineType::get()->apiValue(QuickTimeToolsConversionEngineType::QUICK_TIME_PLAYER_TOOLS))
			return 'KOperationEngineQtTools';
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == QuickTimeToolsConversionEngineType::get()->coreValue(QuickTimeToolsConversionEngineType::QUICK_TIME_PLAYER_TOOLS))
			return 'KDLTranscoderQTPTools';
		
		return null;	
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName)
	{
		if($baseEnumName == 'conversionEngineType')
			return array('QuickTimeToolsConversionEngineType');
			
		return array();
	}
}
