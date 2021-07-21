<?php
/**
 * @package plugins.vendor
 */
class VendorPlugin extends KalturaPlugin implements  IKalturaServices, IKalturaEnumerator
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
	 * @inheritDoc
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (!$baseEnumName)
		{
			return array('VendorIntegrationPrepBatchJobType');
		}

		switch ($baseEnumName)
		{
			case 'BatchJobType':
				return array('VendorIntegrationPrepBatchJobType');
				break;
		}

		return array();
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
