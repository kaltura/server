<?php
class SystemUserPlugin extends KalturaPlugin
{
	public static function getServicesMap()
	{
		$map = array(
			'systemUser' => 'SystemUserService'
		);
		return $map;
	}
	
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/../config/system_user.ct');
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
