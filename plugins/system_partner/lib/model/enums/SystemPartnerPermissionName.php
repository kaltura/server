<?php

/**
 * @package plugins.systemPartner
 * @subpackage model.enum
 */ 
class SystemPartnerPermissionName implements IKalturaPluginEnum, PermissionName
{
	const SYSTEM_ADMIN_PUBLISHER_CONFIG_TECH_DATA = 'SYSTEM_ADMIN_PUBLISHER_CONFIG_TECH_DATA';
	const SYSTEM_ADMIN_PUBLISHER_CONFIG_ACCOUNT_INFO = 'SYSTEM_ADMIN_PUBLISHER_CONFIG_ACCOUNT_INFO';
	const SYSTEM_ADMIN_PUBLISHER_CONFIG_GROUP_OPTIONS = 'SYSTEM_ADMIN_PUBLISHER_CONFIG_GROUP_OPTIONS';
	
	public static function getAdditionalValues()
	{
		return array
		(
			'SYSTEM_ADMIN_PUBLISHER_CONFIG_TECH_DATA' => self::SYSTEM_ADMIN_PUBLISHER_CONFIG_TECH_DATA,
			'SYSTEM_ADMIN_PUBLISHER_CONFIG_ACCOUNT_INFO' => self::SYSTEM_ADMIN_PUBLISHER_CONFIG_ACCOUNT_INFO,
			'SYSTEM_ADMIN_PUBLISHER_CONFIG_GROUP_OPTIONS' => self::SYSTEM_ADMIN_PUBLISHER_CONFIG_GROUP_OPTIONS,
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
