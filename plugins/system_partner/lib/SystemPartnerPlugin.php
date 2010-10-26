<?php
class SystemPartnerPlugin implements KalturaPlugin, KalturaServicesPlugin
{
	const PLUGIN_NAME = 'systemPartner';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function getServicesMap()
	{
		$map = array(
			'systemPartner' => 'SystemPartnerService'
		);
		return $map;
	}
	
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/../config/system_partner.ct');
	}

	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
		
		return false;
	}
}
