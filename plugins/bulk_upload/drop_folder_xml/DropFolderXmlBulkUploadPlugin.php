<?php
/**
 * @package plugins.dropFolderXmlBulkUpload
 */
class DropFolderXmlBulkUploadPlugin extends KalturaPlugin implements IKalturaBulkUpload, IKalturaPending
{
	const PLUGIN_NAME = 'dropFolderXmlBulkUpload';
	const XML_BULK_UPLOAD_PLUGIN_VERSION_MAJOR = 1;
	const XML_BULK_UPLOAD_PLUGIN_VERSION_MINOR = 1;
	const XML_BULK_UPLOAD_PLUGIN_VERSION_BUILD = 0;
	
	/**
	 * Returns the plugin name
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$bulkUploadXmlVersion = new KalturaVersion(
			self::XML_BULK_UPLOAD_PLUGIN_VERSION_MAJOR,
			self::XML_BULK_UPLOAD_PLUGIN_VERSION_MINOR,
			self::XML_BULK_UPLOAD_PLUGIN_VERSION_BUILD);
			
		$bulkUploadXmlDependency = new KalturaDependency(BulkUploadXmlPlugin::getPluginName(), $bulkUploadXmlVersion);
		$dropFolderDependency = new KalturaDependency(DropFolderPlugin::getPluginName());
		
		return array($bulkUploadXmlDependency, $dropFolderDependency);
	}
		
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('DropFolderXmlBulkUploadType', 'DropFolderXmlFileHandlerType');
		
		if($baseEnumName == 'BulkUploadType')
			return array('DropFolderXmlBulkUploadType');
		
		if($baseEnumName == 'DropFolderFileHandlerType')
			return array('DropFolderXmlFileHandlerType');
			
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
		if($baseClass == 'kBulkUploadJobData' && $enumValue == self::getBulkUploadTypeCoreValue(DropFolderXmlBulkUploadType::DROP_FOLDER_XML))
			return new kBulkUploadXmlJobData();
		
		 //Gets the right job for the engine	
		if($baseClass == 'KalturaBulkUploadJobData' && $enumValue == self::getBulkUploadTypeCoreValue(DropFolderXmlBulkUploadType::DROP_FOLDER_XML))
			return new KalturaBulkUploadXmlJobData();
		
		//Gets the engine (only for clients)
		if($baseClass == 'KBulkUploadEngine' && class_exists('KalturaClient') && $enumValue == KalturaBulkUploadType::DROP_FOLDER_XML)
		{
			list($taskConfig, $kClient, $job) = $constructorArgs;
			return new BulkUploadEngineXml($taskConfig, $kClient, $job);
		}
		
		if ($baseClass == 'DropFolderFileHandler' && $enumValue == self::getFileHandlerTypeCoreValue(DropFolderXmlFileHandlerType::XML))
			return new DropFolderXmlBulkUploadFileHandler();
		
		// drop folder does not work in partner services 2 context because it uses dynamic enums
		if (class_exists('kCurrentContext') && kCurrentContext::$ps_vesion == 'ps3')
		{
			if ($baseClass == 'KalturaDropFolderFileHandlerConfig' && $enumValue == self::getFileHandlerTypeCoreValue(DropFolderXmlFileHandlerType::XML))
				return new KalturaDropFolderXmlBulkUploadFileHandlerConfig();
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
		if($enumValue == self::getBulkUploadTypeCoreValue(DropFolderXmlBulkUploadType::DROP_FOLDER_XML))
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
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getFileHandlerTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('DropFolderFileHandlerType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
