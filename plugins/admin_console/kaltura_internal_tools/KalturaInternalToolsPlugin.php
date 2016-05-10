<?php
/**
 * @package plugins.KalturaInternalTools
 */
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
	
	/* (non-PHPdoc)
	 * @see IKalturaAdminConsolePages::getApplicationPages()
	 */
	public static function getApplicationPages()
	{
		$KalturaInternalTools = array(new KalturaInternalToolsPluginSystemHelperAction(),new KalturaInternalToolsPluginFlavorParams());
		return $KalturaInternalTools;
	}
	
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
		
		return false;
	}
}
