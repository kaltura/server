<?php
class KalturaInternalToolsPlugin extends KalturaPlugin implements IKalturaServices, IKalturaAdminConsolePages
{
	const PLUGIN_NAME = 'KalturaInternalTools';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'KalturaInternalTools' => 'KalturaInternalToolsService',
			'KalturaInternalToolsSystemHelper' => 'KalturaInternalToolsSystemHelperService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/config/kaltura_internal_tools.ct');
	}

	/**
	 * @return array<KalturaAdminConsolePlugin>
	 */
	public static function getAdminConsolePages()
	{
		$KalturaInternalTools = new KalturaInternalToolsPluginSystemHelperAction('System Helper', 'KalturaInternalToolsPluginSystemHelper', 'Developer');
		return array($KalturaInternalTools);
	}
	
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
		
		return false;
	}
}
