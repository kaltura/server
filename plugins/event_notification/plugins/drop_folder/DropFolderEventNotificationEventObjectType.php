<?php
/**
 * @package plugins.dropFolderEventNotifications
 * @subpackage lib
 */
class DropFolderEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const DROP_FOLDER_FILE = 'DropFolderFile';
	const DROP_FOLDER = 'DropFolder';
	
	public static function getAdditionalValues()
	{
		return array(
			'DROP_FOLDER_FILE' => self::DROP_FOLDER_FILE,
			'DROP_FOLDER' => self::DROP_FOLDER,
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