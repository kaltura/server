<?php
/**
 * Enable the plugin to define new objects to support custom metadata
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaMetadataObjects extends IKalturaBase, IKalturaEnumerator
{
	/**
	 * Return the correct core object type according to the class name
	 * @param string $className
	 */
	public static function getObjectType($className);
	
	/**
	 * Return the correct class name according to the object type 
	 * @param string $type
	 */
	public static function getObjectClassName($type);
	
	/**
	 * Return the correct object peer according to the object type 
	 * @param string $type
	 */
	public static function getObjectPeer($type);
}