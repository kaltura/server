<?php
/**
 * @package plugins.thumbnail
 */
class ThumbnailPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaPending
{
	const PLUGIN_NAME = 'thumbnail';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		return true;
	}

	public static function dependsOn()
	{
		$dependency = new KalturaDependency(FileSyncPlugin::getPluginName());
		return array($dependency);
	}

	public static function getServicesMap ()
	{
		$map = array(
			'thumbnail' => 'ThumbnailService',
		);

		return $map;
	}
}