<?php
/**
 * @package plugins.DropFolderMrss
 * @subpackage model.enum
 */
class MrssDropFolderFileFileSyncObjectType implements IKalturaPluginEnum, FileSyncObjectType
{
	const MRSS_DROP_FOLDER_FILE = 'MrssDropFolderFile';
	
	public static function getAdditionalValues()
	{
		return array(
			'MRSS_DROP_FOLDER_FILE' => self::MRSS_DROP_FOLDER_FILE,
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
