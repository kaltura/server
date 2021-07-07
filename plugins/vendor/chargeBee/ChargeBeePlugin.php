<?php
/**
 * @package plugins.ChargeBee
 */
class ChargeBeePlugin extends KalturaPlugin implements  IKalturaServices, IKalturaPending
{
	const PLUGIN_NAME = 'chargeBee';

	public static function getServicesMap()
	{
		$map = array(
			'chargeBeeVendor' => 'ChargeBeeVendorService',
		);
		return $map;
	}

	public static function dependsOn()
	{
		$vendorDependency = new KalturaDependency(VendorPlugin::PLUGIN_NAME);
		return array($vendorDependency);
	}

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
}
