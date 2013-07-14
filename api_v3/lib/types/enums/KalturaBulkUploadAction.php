<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBulkUploadAction extends KalturaDynamicEnum implements BulkUploadAction
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'BulkUploadAction';
	}
}
