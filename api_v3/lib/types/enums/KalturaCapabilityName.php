<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaCapabilityName extends KalturaDynamicEnum implements CapabilityName
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'CapabilityName';
	}
}
