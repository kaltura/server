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
		);
	}
}
