<?php
/**
 * @package infra
 * @subpackage Plugins
 */
abstract class KalturaPluginFileSyncObjectManager
{
	/**
	 * 
	 * @param int $objectType
	 * @param string $objectId
	 * @return ISyncableFile
	 */
	abstract public function retrieveObject($objectType, $objectId);
	
}