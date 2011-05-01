<?php
/**
 * @package plugins.dropFolderXmlBulkUpload
 * @subpackage lib
 */
class DropFolderXmlFileHandlerType implements IKalturaPluginEnum, DropFolderFileHandlerType
{
	const XML = 'XML';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'XML' => self::XML,
		);
	}
}
