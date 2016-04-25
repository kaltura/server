<?php
/**
 * @package plugins.scheduleDropFolder
 * @subpackage lib
 */
class DropFolderFileHandlerScheduleType implements IKalturaPluginEnum, DropFolderFileHandlerType
{
	const ICAL = 'ICAL';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'ICAL' => self::ICAL,
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
