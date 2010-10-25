<?php
abstract class KalturaPlugin
{
	/**
	 * @return string the name of the plugin
	 */
	abstract public static function getPluginName();
	
	/**
	 * @param int $partnerId
	 * @return bool
	 */
	public static function isAllowedPartner($partnerId)
	{
		return true;
	}
}