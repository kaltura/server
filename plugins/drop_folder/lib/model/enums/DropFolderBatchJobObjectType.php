<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */
class DropFolderBatchJobObjectType implements IKalturaPluginEnum, BatchJobObjectType
{
	const DROP_FOLDER_FILE		= "DropFolderFile";
	
	public static function getAdditionalValues()
	{
		return array(
			'DROP_FOLDER_FILE' => self::DROP_FOLDER_FILE,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array("Represents drop folder file object");
	}
}