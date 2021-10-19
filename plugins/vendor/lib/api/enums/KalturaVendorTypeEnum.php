<?php
/**
 * @package plugins.vendor
 * @subpackage api.enum
 */
class KalturaVendorTypeEnum extends KalturaDynamicEnum implements VendorTypeEnum
{
	public static function getEnumClass()
	{
		return 'VendorTypeEnum';
	}
}