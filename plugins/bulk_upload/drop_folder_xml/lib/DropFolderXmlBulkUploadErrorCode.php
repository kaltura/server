<?php
/**
 * @package plugins.dropFolderXmlBulkUpload
 * @subpackage lib
 */
class DropFolderXmlBulkUploadErrorCode implements IKalturaPluginEnum, DropFolderFileErrorCode
{
	const LOCAL_FILE_WRONG_SIZE = 'LOCAL_FILE_WRONG_SIZE';
	const LOCAL_FILE_WRONG_CHECKSUM = 'LOCAL_FILE_WRONG_CHECKSUM';
	const ERROR_WRITING_TEMP_FILE = 'ERROR_WRITING_TEMP_FILE';
	const ERROR_ADDING_BULK_UPLOAD = 'ERROR_ADDING_BULK_UPLOAD';
	const ERROR_IN_BULK_UPLOAD = 'ERROR_IN_BULK_UPLOAD';
	const MALFORMED_XML_FILE = 'MALFORMED_XML_FILE';
	const ERROR_ADD_CONTENT_RESOURCE = 'ERROR_ADD_CONTENT_RESOURCE';
	const XML_FILE_SIZE_EXCEED_LIMIT = 'XML_FILE_SIZE_EXCEED_LIMIT';
	
	
	/**
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array (
			'LOCAL_FILE_WRONG_SIZE' => self::LOCAL_FILE_WRONG_SIZE,
			'LOCAL_FILE_WRONG_CHECKSUM' => self::LOCAL_FILE_WRONG_CHECKSUM,
			'ERROR_WRITING_TEMP_FILE' => self::ERROR_WRITING_TEMP_FILE,
			'ERROR_ADDING_BULK_UPLOAD' => self::ERROR_ADDING_BULK_UPLOAD,
			'ERROR_IN_BULK_UPLOAD' => self::ERROR_IN_BULK_UPLOAD,
			'MALFORMED_XML_FILE' => self::MALFORMED_XML_FILE,
			'ERROR_ADD_CONTENT_RESOURCE' => self::ERROR_ADD_CONTENT_RESOURCE,
			'XML_FILE_SIZE_EXCEED_LIMIT' => self::XML_FILE_SIZE_EXCEED_LIMIT,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
