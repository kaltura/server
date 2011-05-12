<?php
/**
 * @package plugins.bulkUploadCsv
 * @subpackage lib
 */
class BulkUploadCsvType implements IKalturaPluginEnum, BulkUploadType
{
	const CSV = 'CSV';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'CSV' => self::CSV,
		);
	}
}
