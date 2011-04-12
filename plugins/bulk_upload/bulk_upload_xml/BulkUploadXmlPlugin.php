<?php
/**
 * @package plugins.bulkUploadXmlEngine
 */
class BulkUploadXmlEnginePlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'bulkUploadXml';
	
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
			return array('BulkUploadXmlType');
		
		if($baseEnumName == 'KalturaBulkUploadType')
			return array('BulkUploadXmlType');
			
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
		//The client side
		if(class_exists('KalturaClient') && $baseClass == 'KalturaBulkUploadJobData')
		{
			//TODO: add support for different job types
			if($enumValue == KalturaBulkUploadType::XML)
				return new kBulkUploadXmlJobData();
		}
				
		//If we are on the client side (like batch)
		if(class_exists('KalturaClient') && $baseClass == 'KBulkUploadEngine')
		{
			if($enumValue == KalturaBulkUploadType::XML)
			{
				return new BulkUploadEngineXml($constructorArgs[0], $constructorArgs[1]);
//				$reflection = new ReflectionClass('BulkUploadEngineXml');	
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
		if(class_exists('KalturaClient') && $baseClass == 'KalturaBulkUploadJobData')
		{
			if($enumValue == self::getApiValue(BulkUploadType::XML))
				return 'kBulkUploadXmlJobData';
		}
		
		if(class_exists('KalturaClient') && $baseClass == 'KBulkUploadEngine')
		{
			if($enumValue == KalturaBulkUploadType::CSV)
			{
				return 'BulkUploadEngineXml';
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
