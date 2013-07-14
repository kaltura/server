<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaTaggedObjectType extends KalturaDynamicEnum implements taggedObjectType
{
	public static function getEnumClass()
	{
		return 'taggedObjectType';
	}
}