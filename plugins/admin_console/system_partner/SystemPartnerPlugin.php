<?php
/**
 * @package plugins.systemPartner
 */
class SystemPartnerPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices, IKalturaConfigurator, IKalturaEnumerator
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
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('SystemPartnerPermissionName');
			
		if($baseEnumName == 'PermissionName')
			return array('SystemPartnerPermissionName');
			
		return array();
	}
}
