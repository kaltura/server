<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaServerNodeType extends KalturaDynamicEnum implements serverNodeType
{
	public static function getEnumClass()
	{
		return 'serverNodeType';
	}
}