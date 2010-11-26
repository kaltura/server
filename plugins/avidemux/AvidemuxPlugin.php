<?php

class AvidemuxPlugin extends KalturaPlugin implements IKalturaObjectLoader, IKalturaEnumerator
{
	const PLUGIN_NAME = 'avidemux';
	
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
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::AVIDEMUX)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
				
			$params = $constructorArgs['params'];
			return new KOperationEngineAvidemux($params->avidemuxCmd, $constructorArgs['outFilePath']);
		}
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == AvidemuxConversionEngineType::get()->apiValue(AvidemuxConversionEngineType::AVIDEMUX))
		{
			return new KDLOperatorAvidemux($enumValue);
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
		if($baseClass == 'KOperationEngine' && $enumValue == AvidemuxConversionEngineType::get()->apiValue(AvidemuxConversionEngineType::AVIDEMUX))
			return 'KOperationEngineFastStart';
	
		if($baseClass == 'KDLOperatorBase' && $enumValue == AvidemuxConversionEngineType::get()->coreValue(AvidemuxConversionEngineType::AVIDEMUX))
			return 'KDLOperatorQTFastStart';
		
		return null;
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName)
	{
		if($baseEnumName == 'conversionEngineType')
			return array('AvidemuxConversionEngineType');
			
		return array();
	}
	
}
