<?php
class StorageProfilePlugin implements IKalturaPermissionsPlugin, IKalturaServicesPlugin
{
	const PLUGIN_NAME = 'storageProfile';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public function getInstances($intrface)
	{
		if($this instanceof $intrface)
			return array($this);
			
		return array();
	}
	
	public static function getServicesMap()
	{
		$map = array(
			'storageProfile' => 'StorageProfileService'
		);
		return $map;
	}
	
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/../config/storage_profile.ct');
	}

	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
		
		return false;
	}
}
