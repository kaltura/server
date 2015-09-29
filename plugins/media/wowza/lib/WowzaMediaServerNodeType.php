<?php
/**
 * @package plugins.wowza
 * @subpackage lib.enum
 */
class WowzaMediaServerNodeType implements IKalturaPluginEnum, serverNodeType
{
	const WOWZA_MEDIA_SERVER = 'WOWZA_MEDIA_SERVER';
	
	public static function getAdditionalValues()
	{
		return array(
			'WOWZA_MEDIA_SERVER' => self::WOWZA_MEDIA_SERVER,
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