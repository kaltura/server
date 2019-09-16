<?php
/**
 * @package plugins.sso
 */
class SsoPlugin extends KalturaPlugin implements  IKalturaServices, IKalturaAdminConsolePages
{
	const PLUGIN_NAME = 'sso';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	//
	public static function getServicesMap()
	{
		$map = array(
			'sso' => 'SsoService',
		);
		return $map;
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getCoreValue($type, $valueName)
	{
		return kPluginableEnumsManager::apiToCore($type, $valueName);
	}

	/*
	 * @see IKalturaAdminConsolePages::getApplicationPages()
	 */
	public static function getApplicationPages()
	{
		$pages = array();
		$pages[] = new SsoProfileListAction();
		$pages[] = new SsoProfileConfigureAction();
		$pages[] = new SsoProfileSetStatusAction();

		return $pages;
	}
}