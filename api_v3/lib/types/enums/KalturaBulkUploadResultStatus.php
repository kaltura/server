<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBulkUploadResultStatus extends KalturaDynamicEnum implements BulkUploadResultStatus
{
	public static function getEnumClass()
	{
		return 'BulkUploadResultStatus';
	}
}