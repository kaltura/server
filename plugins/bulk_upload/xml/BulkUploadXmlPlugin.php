<?php
/**
 * @package plugins.bulkUploadXml
 */
class BulkUploadXmlPlugin extends KalturaPlugin implements IKalturaBulkUpload, IKalturaVersion
{
	const PLUGIN_NAME = 'bulkUploadXml';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 1;
	const PLUGIN_VERSION_BUILD = 0;
	
	/**
	 * Returns the plugin name
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @return KalturaVersion
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);
	}
		
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('BulkUploadXmlType');
		
		if($baseEnumName == 'BulkUploadType')
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
		//Gets the right job for the engine	
		if($baseClass == 'kBulkUploadJobData' && $enumValue == self::getBulkUploadTypeCoreValue(BulkUploadXmlType::XML))
			return new kBulkUploadXmlJobData();
		
		 //Gets the right job for the engine	
		if($baseClass == 'KalturaBulkUploadJobData' && $enumValue == self::getBulkUploadTypeCoreValue(BulkUploadXmlType::XML))
			return new KalturaBulkUploadXmlJobData();
		
		//Gets the engine (only for clients)
		if($baseClass == 'KBulkUploadEngine' && class_exists('KalturaClient') && $enumValue == KalturaBulkUploadType::XML)
		{
			list($taskConfig, $kClient, $job) = $constructorArgs;
			return new BulkUploadEngineXml($taskConfig, $kClient, $job);
		}
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		return null;
	}
	
	/**
	 * Returns the correct file extension for bulk upload type
	 * @param int $enumValue code API value
	 */
	public static function getFileExtension($enumValue)
	{
		if($enumValue == self::getBulkUploadTypeCoreValue(BulkUploadXmlType::XML))
			return 'xml';
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
