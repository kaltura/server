<?php
/**
 * @package plugins.drm
 * @subpackage api.enum
 */
class KalturaDrmLicenseScenario extends KalturaDynamicEnum implements DrmLicenseScenario
{
	public static function getEnumClass()
	{
		return 'DrmLicenseScenario';
	}
}