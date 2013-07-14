<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBulkUploadObjectType extends KalturaDynamicEnum implements BulkUploadObjectType
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'BulkUploadObjectType';
	}
}