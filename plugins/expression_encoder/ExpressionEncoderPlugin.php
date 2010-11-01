<?php

class ExpressionEncoderPlugin extends KalturaPlugin implements IKalturaObjectLoader
{
	const PLUGIN_NAME = 'expressionEncoder';
	
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
		if($objectType == KalturaPluginManager::OBJECT_TYPE_OPERATION_ENGINE && $enumValue == KalturaConversionEngineType::EXPRESSION_ENCODER)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
				
			$params = $constructorArgs['params'];
			return new KOperationExpressionEncoder($params->expEncoderCmd, $constructorArgs['outFilePath']);
		}
			
		if($objectType == KalturaPluginManager::OBJECT_TYPE_KDL_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_EXPRESSION_ENCODER)
		{
			return new KDLOperatorExpressionEncoder($enumValue);
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
		if($objectType == KalturaPluginManager::OBJECT_TYPE_OPERATION_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_EXPRESSION_ENCODER)
			return 'KOperationExpressionEncoder';
			
		if($objectType == KalturaPluginManager::OBJECT_TYPE_KDL_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_EXPRESSION_ENCODER)
			return 'KDLOperatorExpressionEncoder';
		
		return null;
	}
}
