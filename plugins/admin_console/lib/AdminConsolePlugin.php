<?php
class AdminConsolePlugin extends KalturaPlugin
{
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

	public static function getDatabaseConfig()
	{
//		$config = new Zend_Config_Ini(dirname(__FILE__).'/../config/database.ini');
//		return $config->toArray();
	}
	
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
		
		return false;
	}
}
?>
