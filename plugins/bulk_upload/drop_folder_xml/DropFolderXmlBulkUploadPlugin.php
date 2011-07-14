<?php
/**
 * @package plugins.dropFolderXmlBulkUpload
 */
class DropFolderXmlBulkUploadPlugin extends KalturaPlugin implements IKalturaBulkUpload, IKalturaPending, IKalturaSchemaDefiner
{
	const PLUGIN_NAME = 'dropFolderXmlBulkUpload';
	const XML_BULK_UPLOAD_PLUGIN_VERSION_MAJOR = 1;
	const XML_BULK_UPLOAD_PLUGIN_VERSION_MINOR = 1;
	const XML_BULK_UPLOAD_PLUGIN_VERSION_BUILD = 0;
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
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
	
	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('DropFolderXmlBulkUploadType', 'DropFolderXmlFileHandlerType', 'DropFolderXmlBulkUploadErrorCode', 'DropFolderXmlSchemaType');
		
		if($baseEnumName == 'BulkUploadType')
			return array('DropFolderXmlBulkUploadType');
		
		if($baseEnumName == 'DropFolderFileHandlerType')
			return array('DropFolderXmlFileHandlerType');
			
		if($baseEnumName == 'DropFolderFileErrorCode')
			return array('DropFolderXmlBulkUploadErrorCode');
			
		if($baseEnumName == 'SchemaType')
			return array('DropFolderXmlSchemaType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
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
			return new DropFolderXmlBulkUploadEngine($taskConfig, $kClient, $job);
		}
		
		if ($baseClass == 'DropFolderFileHandler' && $enumValue == KalturaDropFolderFileHandlerType::XML)
				return new DropFolderXmlBulkUploadFileHandler();
		
		// drop folder does not work in partner services 2 context because it uses dynamic enums
		if (class_exists('kCurrentContext') && kCurrentContext::$ps_vesion == 'ps3')
		{			
			if ($baseClass == 'KalturaDropFolderFileHandlerConfig' && $enumValue == self::getFileHandlerTypeCoreValue(DropFolderXmlFileHandlerType::XML))
				return new KalturaDropFolderXmlBulkUploadFileHandlerConfig();
		}
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaBulkUpload::getFileExtension()
	 */
	public static function getFileExtension($enumValue)
	{
		if($enumValue == self::getBulkUploadTypeCoreValue(DropFolderXmlBulkUploadType::DROP_FOLDER_XML))
			return 'xml';
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::getPluginSchema()
	 */
	public static function getPluginSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != self::getSchemaTypeCoreValue(DropFolderXmlSchemaType::DROP_FOLDER_XML))
			return null;
			
		$xmlnsBase = "http://" . kConf::get('www_host') . "/$type";
		$xmlnsPlugin = "http://" . kConf::get('www_host') . "/$type/" . self::getPluginName();
		
		$xsd = '<?xml version="1.0" encoding="UTF-8"?>
			<xs:schema 
				xmlns:xs="http://www.w3.org/2001/XMLSchema"
				xmlns="' . $xmlnsPlugin . '" 
				targetNamespace="' . $xmlnsPlugin . '"
			>
				
				<xs:complexType name="T_serverFileContentResource">
					<xs:complexContent>
						<xs:extension base="T_serverFileContentResource">
							<xs:attribute name="dropFolderFileId" type="xs:string" use="optional"/>
						</xs:extension>
					</xs:complexContent>
				</xs:complexType>
				
				<xs:element name="dropFolderFileContentResource" type="T_serverFileContentResource" substitutionGroup="core:serverFileContentResource" />
			</xs:schema>
		';
		
		return $xsd;
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
	public static function getSchemaTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('SchemaType', $value);
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
