<?php

/**
 * @package plugins.beacon
 * @subpackage api.enum
 */
class KalturaBeaconIndexType extends KalturaStringEnum implements BeaconIndexType
{
	public static function getEnumClass()
	{
		return 'BeaconIndexType';
	}
}