<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaSchemaType extends KalturaDynamicEnum implements SchemaType
{
	public static function getEnumClass()
	{
		return 'SchemaType';
	}
}
