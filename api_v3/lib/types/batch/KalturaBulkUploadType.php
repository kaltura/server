<?php

/**
 * 
 * Represents the bulk upload type
 * @author Roni
 * @package api
 * @subpackage objects
 *
 */
class KalturaBulkUploadType extends KalturaDynamicEnum implements BulkUploadType
{
	public static function getEnumClass()
	{
		return 'BulkUploadType';
	}
}