<?php
/**
 * @package plugins.scheduleDropFolder
 * @subpackage lib
 */
class DropFolderScheduleType implements IKalturaPluginEnum, BulkUploadType
{
	const DROP_FOLDER_ICAL = 'DROP_FOLDER_ICAL';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'DROP_FOLDER_ICAL' => self::DROP_FOLDER_ICAL,
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
