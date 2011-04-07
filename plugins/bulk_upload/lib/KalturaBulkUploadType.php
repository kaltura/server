<?php
/**
 * @package plugins.bulkUpload
 * @subpackage lib
 */
class KalturaBulkUploadType extends KalturaEnum implements BulkUploadType
{
	const XML = 'XML';
	const CSV = 'CSV';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		//TODO: Roni - see if this is ok that the additional values are the same as the values
		return array(
			'XML' => self::XML,
			'CSV' => self::CSV,
		);
	}
}
