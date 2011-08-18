<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaSchemaType extends KalturaDynamicEnum implements SchemaType
{
	public static function getDescriptions()
	{
		$descriptions = array(
			self::SYNDICATION => 'Syndication feed'
		);
		
		return self::mergeDescriptions(self::getEnumClass(), $descriptions);
	}

	public static function getEnumClass()
	{
		return 'SchemaType';
	}
}
