<?php
/**
 * @package plugins.dropFolderXmlBulkUpload
 * @subpackage lib
 */
class DropFolderXmlSchemaType implements IKalturaPluginEnum, SchemaType
{
	const DROP_FOLDER_XML = 'dropFolderXml';
	
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
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			DropFolderXmlBulkUploadPlugin::getApiValue(self::DROP_FOLDER_XML) => 'Drop folder',
		);
	}
}
