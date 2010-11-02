<?php

class ExpressionEncoderPlugin extends KalturaPlugin implements IKalturaObjectLoader
{
	const PLUGIN_NAME = 'expressionEncoder';
	
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
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::EXPRESSION_ENCODER)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
				
			$params = $constructorArgs['params'];
			return new KOperationExpressionEncoder($params->expEncoderCmd, $constructorArgs['outFilePath']);
		}
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == kConvertJobData::CONVERSION_ENGINE_EXPRESSION_ENCODER)
		{
			return new KDLOperatorExpressionEncoder($enumValue);
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
		if($baseClass == 'KOperationEngine' && $enumValue == kConvertJobData::CONVERSION_ENGINE_EXPRESSION_ENCODER)
			return 'KOperationExpressionEncoder';
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == kConvertJobData::CONVERSION_ENGINE_EXPRESSION_ENCODER)
			return 'KDLOperatorExpressionEncoder';
		
		return null;
	}
}
