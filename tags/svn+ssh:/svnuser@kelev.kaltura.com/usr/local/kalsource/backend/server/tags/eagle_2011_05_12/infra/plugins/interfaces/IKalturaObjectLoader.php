<?php
/**
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaObjectLoader extends IKalturaBase
{
	/**
	 * Returns an object that is known only to the plugin, and extends the baseClass.
	 * 
	 * @param string $baseClass The base class of the loaded object
	 * @param string $enumValue The enumeration value of the loaded object
	 * @param array $constructorArgs The constructor arguments of the loaded object
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null);
	
	/**
	 * Retrieves a class name that is defined by the plugin and is known only to the plugin, and extends the baseClass.
	 * 
	 * @param string $baseClass The base class of the searched class
	 * @param string $enumValue The enumeration value of the searched class
	 * @return string The name of the searched object's class
	 */
	public static function getObjectClass($baseClass, $enumValue);
}