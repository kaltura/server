<?php

/**
 * Sending beacons on various objects
 * @package plugins.beacon
 */
class BeaconPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaPending, IKalturaEventConsumers, IKalturaEnumerator, IKalturaObjectLoader
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
	
	/* (non-PHPdoc)
 	 * @see IKalturaEventConsumers::getEventConsumers()
 	 */
	public static function getEventConsumers()
	{
		//TODO: Once delete support is add BEACON_MANAGER to events consumern list 
		return array(
			//self::BEACON_MANAGER,
		);
	}
	
	/* (non-PHPdoc)
 * @see IKalturaEnumerator::getEnums()
 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('ClearBeaconsBatchType');
		
		if($baseEnumName == 'BatchJobType')
			return array('ClearBeaconsBatchType');
		
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		
		if($baseClass == 'kJobData' && $enumValue == self::getBatchJobTypeCoreValue(ClearBeaconsBatchType::CLEAR_BEACONS))
			return new kClearBeconsJobData();
		
		if($baseClass == 'KalturaJobData' && $enumValue == self::getApiValue(ClearBeaconsBatchType::CLEAR_BEACONS))
			return new KalturaClearBeaconsJobData();
		
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'kJobData' && $enumValue == self::getBatchJobTypeCoreValue(ClearBeaconsBatchType::CLEAR_BEACONS))
			return 'kClearBeconsJobData';
		
		if($baseClass == 'KalturaJobData' && $enumValue == self::getApiValue(ClearBeaconsBatchType::CLEAR_BEACONS))
			return 'KalturaClearBeaconsJobData';
		
		return null;
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBatchJobTypeCoreValue($valueName)
	{
		$value = self::getApiValue($valueName);
		return kPluginableEnumsManager::apiToCore('BatchJobType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
