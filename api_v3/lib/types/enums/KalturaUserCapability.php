<?php

/**
 * @package api
 * @subpackage enum
 */
class KalturaUserCapability extends KalturaDynamicEnum implements UserCapability
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'UserCapability';
	}
}
