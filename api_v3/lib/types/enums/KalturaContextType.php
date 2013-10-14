<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaContextType extends KalturaDynamicEnum implements ContextType
{
	public static function getEnumClass()
	{
		return 'ContextType';
	}
}