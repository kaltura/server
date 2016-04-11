<?php
/**
 * @package plugins.scheduleBulkUpload
 * @subpackage lib
 */
class BulkUploadScheduleType implements IKalturaPluginEnum, BulkUploadType
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
