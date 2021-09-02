<?php

/**
 * @package plugins.drm
 * @subpackage model.enum
 */ 
class DrmPermissionName implements IKalturaPluginEnum, PermissionName
{
	const SYSTEM_ADMIN_DRM_PROFILE_BASE = 'SYSTEM_ADMIN_DRM_PROFILE_BASE';
	const SYSTEM_ADMIN_DRM_PROFILE_MODIFY = 'SYSTEM_ADMIN_DRM_PROFILE_MODIFY';
	const SYSTEM_ADMIN_DRM_POLICY_BASE = 'SYSTEM_ADMIN_DRM_POLICY_BASE';
	const SYSTEM_ADMIN_DRM_POLICY_MODIFY = 'SYSTEM_ADMIN_DRM_POLICY_MODIFY';

	public static function getAdditionalValues()
	{
		return array
		(
			'SYSTEM_ADMIN_DRM_PROFILE_BASE' => self::SYSTEM_ADMIN_DRM_PROFILE_BASE,
			'SYSTEM_ADMIN_DRM_PROFILE_MODIFY' => self::SYSTEM_ADMIN_DRM_PROFILE_MODIFY,
			'SYSTEM_ADMIN_DRM_POLICY_BASE' => self::SYSTEM_ADMIN_DRM_POLICY_BASE,
			'SYSTEM_ADMIN_DRM_POLICY_MODIFY' => self::SYSTEM_ADMIN_DRM_POLICY_MODIFY,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
