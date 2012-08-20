<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaAccessControlContextType extends KalturaDynamicEnum implements accessControlContextType
{
	public static function getEnumClass()
	{
		return 'accessControlContextType';
	}
}