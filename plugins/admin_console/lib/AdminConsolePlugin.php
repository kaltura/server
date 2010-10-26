<?php
class AdminConsolePlugin implements KalturaPlugin, KalturaServicesPlugin
{
	const PLUGIN_NAME = 'adminConsole';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function getServicesMap()
	{
		$map = array(
			'flavorParamsOutput' => 'FlavorParamsOutputService',
			'mediaInfo' => 'MediaInfoService',
			'entryAdmin' => 'EntryAdminService',
		);
		return $map;
	}
	
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/../config/admin_console.ct');
	}

	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
		
		return false;
	}
}
