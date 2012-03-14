<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaObjectType extends KalturaDynamicEnum implements objectType
{
	public static function getEnumClass()
	{
		return 'objectType';
	}
}