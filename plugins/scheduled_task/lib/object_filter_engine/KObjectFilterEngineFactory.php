<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectFilterEngine
 */
class KObjectFilterEngineFactory
{
	/**
	 * @param $type
	 * @param KalturaClient $client
	 * @return KObjectFilterEngineBase
	 */
	public static function getInstanceByType($type, KalturaClient $client)
	{
		switch($type)
		{
			case KalturaObjectFilterEngineType::ENTRY:
				return new KObjectFilterBaseEntryEngine($client);
			case KalturaObjectFilterEngineType::ENTRY_VENDOR_TASK:
				return new KObjectFilterEntryVendorTaskEngine($client);
			default:
				return KalturaPluginManager::loadObject('KObjectFilterEngineBase', $type, array($client));
		}
	}
} 