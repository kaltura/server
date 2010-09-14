<?php
class KalturaPlugin
{
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		return array();
	}
	
	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array();
	}
	
	/**
	 * @return array
	 */
	public static function getAdminConsolePages()
	{
		return array();
	}
	
	/**
	 * @param string $entryId the new created entry
	 * @param array $data key => value pairs
	 */
	public static function handleBulkUploadData($entryId, array $data)
	{
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return null;
	}
	
	/**
	 * @return array
	 */
	public static function getDatabaseConfig()
	{
		return null;
	}
	
	/**
	 * @param int $partnerId
	 * @return bool
	 */
	public static function isAllowedPartner($partnerId)
	{
		return true;
	}
	
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($objectType, $enumValue, array $constructorArgs = null)
	{
		return null;
	}
	
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @return object
	 */
	public static function getObjectClass($objectType, $enumValue)
	{
		return null;
	}
	
	
}