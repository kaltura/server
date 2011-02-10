<?php
/**
 * @package plugins.adminConsole
 */
class AdminConsolePlugin extends KalturaPlugin implements IKalturaPlugin, IKalturaPermissions, IKalturaServices
{
	const PLUGIN_NAME = 'adminConsole';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		return null;
	}
		
	public static function getServicesMap()
	{
		$map = array(
			'flavorParamsOutput' => 'FlavorParamsOutputService',
			'thumbParamsOutput' => 'ThumbParamsOutputService',
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
