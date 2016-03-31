<?php
/**
 * @package plugins.scheduleBulkUpload
 * @subpackage lib
 */
class BulkUploadObjectScheduleType implements IKalturaPluginEnum, BulkUploadObjectType
{
	const SCHEDULE_EVENT = 'SCHEDULE_EVENT';
	const SCHEDULE_RESOURCE = 'SCHEDULE_RESOURCE';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'SCHEDULE_EVENT' => self::SCHEDULE_EVENT,
			'SCHEDULE_RESOURCE' => self::SCHEDULE_RESOURCE,
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
