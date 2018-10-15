<?php
/**
 * @package plugins.vendor
 */
class VendorPlugin extends KalturaPlugin implements  IKalturaServices
{
	const PLUGIN_NAME = 'vendor';
	const VENDOR_MANAGER = 'kVendorManager';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}


	public static function getServicesMap()
	{
		$map = array(
			'zoomVendor' => 'ZoomVendorService',
		);
		return $map;
	}

}
