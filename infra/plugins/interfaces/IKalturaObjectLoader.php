<?php
interface IKalturaObjectLoader extends IKalturaBase
{
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($objectType, $enumValue, array $constructorArgs = null);
	
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($objectType, $enumValue);
}