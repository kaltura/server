<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaSsoStatus extends KalturaDynamicEnum implements VendorStatus
{
	public static function getEnumClass()
	{
		return 'VendorStatus';
	}
}

