<?php

class ExpressionEncoderPlugin extends KalturaPlugin implements IKalturaObjectLoader, IKalturaEnumerator
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
		if($baseClass == 'KOperationEngine' && $enumValue == ExpressionEncoderConversionEngineType::get()->apiValue(ExpressionEncoderConversionEngineType::EXPRESSION_ENCODER))
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
				
			$params = $constructorArgs['params'];
			return new KOperationEngineExpressionEncoder($params->expEncoderCmd, $constructorArgs['outFilePath']);
		}
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == ExpressionEncoderConversionEngineType::get()->coreValue(ExpressionEncoderConversionEngineType::EXPRESSION_ENCODER))
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
		if($baseClass == 'KOperationEngine' && $enumValue == ExpressionEncoderConversionEngineType::coreValue(ExpressionEncoderConversionEngineType::EXPRESSION_ENCODER))
			return 'KOperationExpressionEncoder';
			
		if($baseClass == 'KDLOperatorBase' && $enumValue == ExpressionEncoderConversionEngineType::coreValue(ExpressionEncoderConversionEngineType::EXPRESSION_ENCODER))
			return 'KDLOperatorExpressionEncoder';
		
		return null;
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName)
	{
		if($baseEnumName == 'conversionEngineType')
			return array('ExpressionEncoderConversionEngineType');
			
		return array();
	}
}
