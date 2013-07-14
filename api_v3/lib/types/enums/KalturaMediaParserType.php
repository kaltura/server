<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaMediaParserType extends KalturaDynamicEnum implements mediaParserType
{
	public static function getEnumClass()
	{
		return 'mediaParserType';
	}
}
