<?php
/**
 * @package plugins.adminConsoleGallery
 */
class AdminConsoleGalleryPlugin extends KalturaPlugin implements IKalturaAdminConsolePages
{
	const PLUGIN_NAME = 'adminConsoleGallery';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	public static function getAdminConsolePages()
	{
		$pages = array();
		$pages[] = new AdminConsoleGalleryAction();
		return $pages;
	}
}
