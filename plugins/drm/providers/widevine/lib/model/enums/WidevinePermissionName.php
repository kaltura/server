<?php
/**
 * @package plugins.widevine
 * @subpackage model.enum
 */ 
class WidevinePermissionName implements IKalturaPluginEnum, PermissionName
{
	const WIDEVINE_BASE = 'WIDEVINE_BASE';
	
	public static function getAdditionalValues()
	{
		return array
		(
			'WIDEVINE_BASE' => self::WIDEVINE_BASE,
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
