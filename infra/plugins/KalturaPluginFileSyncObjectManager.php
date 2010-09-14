<?php

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