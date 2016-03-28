<?php
/**
 * @package plugins.scheduleBulkUpload
 * @subpackage lib
 */
class BulkUploadObjectScheduleType implements IKalturaPluginEnum, BulkUploadObjectType
{
	const SCHEDULE_EVENT = 'SCHEDULE_EVENT';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'SCHEDULE_EVENT' => self::SCHEDULE_EVENT,
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
