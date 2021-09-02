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
			'vendorIntegration' => 'VendorIntegrationService',
		);
		return $map;
	}

	/**
	 * @param $valueName
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
