<?php
/**
 * @package plugins.metadata
 * @subpackage lib.plugins
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