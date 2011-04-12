<?php
/**
 * @package plugins.bulkUploadCsv
 */
class BulkUploadCsvPlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaObjectLoader, IKalturaBulkUploadDefaultEngine
{
	const PLUGIN_NAME = 'bulkUploadCsv';

	/**
	 * 
	 * Returns wheter this is the default plugin for the bulk upload (in case no bulk upload type was specified)
	 */
	public static function isDefaultEngine()
	{
		return true;
	} 
	
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
	
		if($baseEnumName == 'KalturaBulkUploadType')
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
		//Gets the right job for the engine	
		if(class_exists('KalturaClient') && $baseClass == 'KalturaBulkUploadJobData')
		{
			if($enumValue == BulkUploadType::CSV)
				return new kBulkUploadCsvJobData();
		}
		
		if(class_exists('KalturaClient') && ($enumValue == null) && $baseClass == 'KalturaBulkUploadJobData')
		{
			return new kBulkUploadXmlJobData();
		}
		
		//Gets the engine
		if(class_exists('KalturaClient') && $baseClass == 'KBulkUploadEngine')
		{
			if($enumValue == KalturaBulkUploadType::CSV)
			{
				return new BulkUploadEngineCsv($constructorArgs[0], $constructorArgs[1], $constructorArgs[2]);
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
		//Gets the right job for the engine	
		if(class_exists('KalturaClient') && $baseClass == 'KalturaBulkUploadJobData')
		{
			if($enumValue == BulkUploadType::CSV)
				return 'kBulkUploadCsvJobData';
		}
		
		//Gets the engine
		if(class_exists('KalturaClient') && $baseClass == 'KBulkUploadEngine')
		{
			if($enumValue == KalturaBulkUploadType::CSV)
			{
				return 'BulkUploadEngineCsv';
			}
		}
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
