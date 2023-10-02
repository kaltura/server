<?php

/**
 * @package api
 * @subpackage enum
 */
class KalturaUserCapabilityType extends KalturaDynamicEnum implements UserCapabilityType
{
	public static function getEnumClass()
	{
		return 'UserCapabilityType';
	}
}
