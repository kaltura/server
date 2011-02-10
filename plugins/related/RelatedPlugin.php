<?php
/**
 * Related plugin exposes specific functionality to retreive related entries
 * 
 * @package plugins.related
 */
class RelatedPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices
{
	const PLUGIN_NAME = 'related';
	
	/**
	 * @return string
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @param int $partnerId
	 * @return bool
	 */
	public static function isAllowedPartner($partnerId)
	{
		return true; // plugin is allowed for all partners
	}
	
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'related' => 'RelatedService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/config/related.ct');
	}
}
