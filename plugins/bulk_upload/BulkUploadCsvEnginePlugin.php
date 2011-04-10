<?php
/**
 * @package plugins.bulkUploadCsvEngine
 */
class BulkUploadCsvEnginePlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'bulkUploadCsvEngine';

	/**
	 * 
	 * Returns the plugin name
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('BulkUploadCsvType');
	
		if($baseEnumName == 'BulkUploadType')
			return array('BulkUploadCsvType');
		
		return array();
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		//Gets the right job type for the engine
		if($baseClass == 'kJobData')
		{
			if($enumValue == self::getBulkUploadTypeCoreValue(BulkUploadType::CSV))
				return new kBulkUploadCsvJobData();	
		}
		
		if($baseClass == 'KalturaBulkUploadJobData')
		{
			if($enumValue == self::getApiValue(BulkUploadType::CSV))
				return new kBulkUploadCsvJobData();
		}
		
		//Gets the engine
		if(class_exists('KalturaClient') && $baseClass == 'KBulkUploadEngine')
		{
			if($enumValue == KalturaBulkUploadType::CSV)
			{
				return new BulkUploadEngineCsv($constructorArgs[0], $constructorArgs[1]);
//				$reflection = new ReflectionClass('BulkUploadEngineCsv');
//				return $reflection->newInstance($constructorArgs);
			}
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
		//TODO: check if needed
		return null;
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBulkUploadTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BulkUploadType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
