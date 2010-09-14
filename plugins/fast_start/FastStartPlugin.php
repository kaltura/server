<?php

class FastStartPlugin extends KalturaPlugin
{
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($objectType, $enumValue, array $constructorArgs = null)
	{
		if($objectType == KalturaPluginManager::OBJECT_TYPE_OPERATION_ENGINE && $enumValue == KalturaConversionEngineType::FAST_START)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
				
			$params = $constructorArgs['params'];
			return new KOperationEngineFastStart($params->fastStartCmd, $constructorArgs['outFilePath']);
		}
	
		if($objectType == KalturaPluginManager::OBJECT_TYPE_KDL_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_FAST_START)
		{
			return new KDLOperatorQTFastStart($enumValue);
		}
		
		return null;
	}
}
