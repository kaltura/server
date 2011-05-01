<?php
/**
 * @package plugins.dropFolderXmlBulkUpload
 * @subpackage lib
 */
class DropFolderXmlBulkUploadType implements IKalturaPluginEnum, BulkUploadType
{
	const DROP_FOLDER_XML = 'DROP_FOLDER_XML';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'DROP_FOLDER_XML' => self::DROP_FOLDER_XML,
		);
	}
}
