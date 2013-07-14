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
	
	/* (non-PHPdoc)
	 * @see IKalturaAdminConsolePages::getApplicationPages()
	 */
	public static function getApplicationPages()
	{
		$pages = array();
		$pages[] = new AdminConsoleGalleryAction();
		return $pages;
	}
}
