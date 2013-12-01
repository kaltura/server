<?php
/**
 * @package plugins.bulkUploadCsv
 * @subpackage lib
 */
class BulkUploadFilterType implements IKalturaPluginEnum, BulkUploadType
{
	const FILTER = 'FILTER';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'FILTER' => self::FILTER,
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
