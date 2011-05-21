<?php
/**
 * @package plugins.storageProfile
 */
class StorageProfilePlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices, IKalturaConfigurator
{
	const PLUGIN_NAME = 'storageProfile';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function getServicesMap()
	{
		$map = array(
			'storageProfile' => 'StorageProfileService'
		);
		return $map;
	}
	
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'testme')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/testme.ini');
			
		return null;
	}
}
