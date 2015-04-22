<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaIndexObjectType extends KalturaDynamicEnum implements IndexObjectType
{
	public static function getEnumClass()
	{
		return 'IndexObjectType';
	}
}