<?php
/**
 * @package plugins.bulkUpload
 * @subpackage lib
 */
class BulkUploadXmlType implements IKalturaPluginEnum, BulkUploadType
{
	const XML = 'XML';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'XML' => self::XML,
		);
	}
}
