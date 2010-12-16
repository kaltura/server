<?php
abstract class KalturaPlugin implements IKalturaPlugin
{
	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		return null;
	}
	
	public static function isApiV3()
	{
		if (defined("KALTURA_API_V3")) {
			return true;
		}
		
		return false;
	}
	
	public static function getPluginPermissionName()
	{
		return strtoupper(self::getPluginName() .'_PLUGIN_USAGE');
	}
}