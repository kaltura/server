<?php

/**
 * @package api
 * @subpackage enum
 */
class KalturaUserCapabilityEnum extends KalturaDynamicEnum implements UserCapabilityEnum
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'UserCapabilityEnum';
	}
}
