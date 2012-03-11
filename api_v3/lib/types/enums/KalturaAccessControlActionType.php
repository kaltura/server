<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaAccessControlActionType extends KalturaDynamicEnum implements accessControlActionType
{
	public static function getEnumClass()
	{
		return 'accessControlActionType';
	}
}