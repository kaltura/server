<?php
/**
 * @package plugins.sso
 */
class SsoPlugin extends KalturaPlugin implements  IKalturaServices, IKalturaEnumerator , IKalturaPending
{
	const PLUGIN_NAME = 'sso';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	public static function getServicesMap()
	{
		$map = array(
			'sso' => 'SsoService',
		);
		return $map;
	}

	public static function dependsOn()
	{
		$vendorIntegrationDependency = new KalturaDependency(VendorPlugin::getPluginName());
		return array($vendorIntegrationDependency);
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if (is_null($baseEnumName))
		{
			return array('SsoApplicationType');
		}
		if ($baseEnumName === 'ApplicationType' || $baseEnumName ==='SsoApplicationType')
		{
			return array('SsoApplicationType');
		}
		return array();
	}

	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getCoreValue($type, $valueName)
	{
		return kPluginableEnumsManager::apiToCore($type, $valueName);
	}
}