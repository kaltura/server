<?php
/**
 * @package plugins.scheduleBulkUpload
 * @subpackage lib
 */
class BulkUploadScheduleAction implements IKalturaPluginEnum, BulkUploadAction
{
	const CANCEL = 'CANCEL';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'CANCEL' => self::CANCEL,
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
