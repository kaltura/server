<?php
/**
 * Sending beacons on various objects
 * @package plugins.confControl
 */
class ConfControlPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaAdminConsolePages
{
	const PLUGIN_NAME = 'confControl';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		return ($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID);
	}

	public static function getServicesMap ()
	{
		$map = array(
			'confControl' => 'ConfControlService',
		);
		return $map;
	}

	/*
	 * @see IKalturaAdminConsolePages::getApplicationPages()
	 */
	public static function getApplicationPages()
	{
		$pages = array();
		$pages[] = new ConfigurationMapListAction();
		$pages[] = new ConfigurationMapConfigureAction();
		return $pages;
	}
}