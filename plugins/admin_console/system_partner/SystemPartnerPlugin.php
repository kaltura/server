<?php
/**
 * @package plugins.systemPartner
 */
class SystemPartnerPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices, IKalturaEnumerator
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
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID || $partnerId == Partner::BATCH_PARTNER_ID)
			return true;
		
		return false;
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
