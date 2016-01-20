<?php
/**
 * @package plugins.nginxLive
 * @subpackage lib.enum
 */
class NginxLiveMediaServerNodeType implements IKalturaPluginEnum, serverNodeType
{
	const NGINX_LIVE_MEDIA_SERVER = 'NGINX_LIVE_MEDIA_SERVER';
	
	public static function getAdditionalValues()
	{
		return array(
			'NGINX_LIVE_MEDIA_SERVER' => self::NGINX_LIVE_MEDIA_SERVER,
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