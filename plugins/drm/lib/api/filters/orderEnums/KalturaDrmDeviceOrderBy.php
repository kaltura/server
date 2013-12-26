<?php
/**
 * @package plugins.drm
 * @subpackage api.filters.enum
 */
class KalturaDrmDeviceOrderBy extends KalturaStringEnum
{
	const ID_ASC = "+id";
	const ID_DESC = "-id";
	const USER_ID_ASC = "+userId";
	const USER_ID_DESC = "-userId";
	const DEVICE_ID_ASC = "+deviceId";
	const DEVICE_ID_DESC = "-deviceId";
	const VERSION_ASC = "+version";
	const VERSION_DESC = "-version";
}
