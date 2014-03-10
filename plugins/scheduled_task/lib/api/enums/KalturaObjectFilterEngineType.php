<?php

/**
 * @package plugins.scheduledTask
 * @subpackage api.enum
 * @see ObjectFilterEngineType
 */
class KalturaObjectFilterEngineType extends KalturaDynamicEnum implements ObjectFilterEngineType
{
	public static function getEnumClass()
	{
		return 'ObjectFilterEngineType';
	}
}