<?php

/**
 * @package plugins.beacon
 * @subpackage api.enum
 */
class KalturaBeaconObjectTypes extends KalturaDynamicEnum implements BeaconObjectTypes
{
	public static function getEnumClass()
	{
		return 'BeaconObjectTypes';
	}
}