<?php
/**
 * @package plugins.drm
 * @subpackage api.enum
 */
class KalturaDrmProviderType extends KalturaDynamicEnum implements DrmProviderType
{
	public static function getEnumClass()
	{
		return 'DrmProviderType';
	}
}