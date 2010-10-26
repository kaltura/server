<?php
interface KalturaPlugin
{
	/**
	 * @return string the name of the plugin
	 */
	public static function getPluginName();
	
	/**
	 * @param int $partnerId
	 * @return bool
	 */
	public static function isAllowedPartner($partnerId);
}