<?php
/**
 * @package plugins.smoothProtect
 */
class SmoothProtectPlugin extends KalturaPlugin implements IKalturaObjectLoader, IKalturaEnumerator, IKalturaPending, IKalturaBatchJobDataContributor
{
	const PLUGIN_NAME = 'smoothProtect';
	const PARAMS_STUB = '__params__';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$playReadyDependency = new KalturaDependency(PlayReadyPlugin::getPluginName());
		$ismIndexDependency = new KalturaDependency(IsmIndexPlugin::getPluginName());
		
		return array($playReadyDependency, $ismIndexDependency);
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::SMOOTHPROTECT)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
				
			$params = $constructorArgs['params'];
			return new KOperationEngineSmoothProtect($params->smoothProtectCmd, $constructorArgs['outFilePath']);
		}
	
		if($baseClass == 'KDLOperatorBase' && $enumValue == self::getApiValue(SmoothProtectConversionEngineType::SMOOTHPROTECT))
		{
			return new KDLOperatorSmoothProtect($enumValue);
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
		if($baseClass == 'KOperationEngine' && $enumValue == self::getApiValue(SmoothProtectConversionEngineType::SMOOTHPROTECT))
			return 'KOperationEngineSmoothProtect';
	
		if($baseClass == 'KDLOperatorBase' && $enumValue == self::getConversionEngineCoreValue(SmoothProtectConversionEngineType::SMOOTHPROTECT))
			return 'KDLOperatorSmoothProtect';
		
		return null;
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('SmoothProtectConversionEngineType');
	
		if($baseEnumName == 'conversionEngineType')
			return array('SmoothProtectConversionEngineType');
			
		return array();
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getConversionEngineCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('conversionEngineType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	public static function contributeToJobData ($jobType, $jobSubType, kJobData $jobData)
	{
		if($jobType == BatchJobType::CONVERT
			&& $jobSubType == self::getApiValue(SmoothProtectConversionEngineType::SMOOTHPROTECT)
			&& $jobData instanceof kConvertJobData)
			return IsmIndexPlugin::addIsmManifestsToSrcFileSyncDesc($jobData);
		else 
			return $jobData;
	}
}
