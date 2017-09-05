<?php

/**
 * Sending beacons on various objects
 * @package plugins.beacon
 */
class BeaconPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaPending
{
	const PLUGIN_NAME = "beacon";
	const BEACON_MANAGER = 'kBeaconManager';
	
	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array(
			'beacon' => 'BeaconService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		return true;
	}
	
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
 	 * @see IKalturaPending::dependsOn()
 	*/
	public static function dependsOn()
	{
		$rabbitMqDependency = new KalturaDependency(RabbitMQPlugin::getPluginName());
		$elasticSearchDependency = new KalturaDependency(ElasticSearchPlugin::getPluginName());
		return array($rabbitMqDependency, $elasticSearchDependency);
	}
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
