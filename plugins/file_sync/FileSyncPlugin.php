<?php
/**
 * @package plugins.fileSync
 */
class FileSyncPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaServices
{
	const PLUGIN_NAME = 'fileSync';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public static function getServicesMap()
	{
		$map = array(
			'fileSync' => 'FileSyncService'
		);
		return $map;
	}
	
	public static function isAllowedPartner($partnerId)
	{
		if($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID || $partnerId == Partner::BATCH_PARTNER_ID || $partnerId == Partner::MEDIA_SERVER_PARTNER_ID)
			return true;
		
		return false;
	}
}
