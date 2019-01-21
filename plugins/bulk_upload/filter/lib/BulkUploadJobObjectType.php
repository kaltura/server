<?php
/**
 * @package plugins.bulkUploadFilter
 * @subpackage api.enum
 */
class BulkUploadJobObjectType implements IKalturaPluginEnum, BulkUploadObjectType
{
	
	const JOB = 'JOB';
	
	public static function getAdditionalValues()
	{
		return array(
			'JOB' => self::JOB,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			BulkUploadFilterPlugin::getApiValue(self::JOB) => 'Job',
		);
	}
}