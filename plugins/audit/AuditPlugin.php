<?php
class AuditPlugin extends KalturaPlugin
{
	const PLUGIN_NAME = 'audit';
	const AUDIT_TRAIL_MANAGER = 'kAuditTrailManager';
	
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'auditTrail' => 'AuditTrailService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/config/audit.ct');
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::AUDIT_TRAIL_MANAGER,
		);
	}
	
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($objectType, $enumValue, array $constructorArgs = null)
	{
		// TODO - load data parsers
	}
}
