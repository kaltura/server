<?php
/**
 * @package plugins.drm
 * @subpackage api.enum
 */
class KalturaDrmLicenseType extends KalturaDynamicEnum implements DrmLicenseType
{
	public static function getEnumClass()
	{
		return 'DrmLicenseType';
	}
}