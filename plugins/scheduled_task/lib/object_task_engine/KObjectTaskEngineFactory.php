<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
class KObjectTaskEngineFactory
{
	public static function getInstanceByType($type)
	{
		switch($type)
		{
			case ObjectTaskType::DELETE_ENTRY:
				return new KObjectTaskDeleteEntryEngine();
			case ObjectTaskType::MODIFY_CATEGORIES:
				return new KObjectTaskModifyCategoriesEngine();
			case ObjectTaskType::DELETE_ENTRY_FLAVORS:
				return new KObjectTaskDeleteEntryFlavorsEngine();
			case ObjectTaskType::CONVERT_ENTRY_FLAVORS:
				return new KObjectTaskConvertEntryFlavorsEngine();
			case ObjectTaskType::DELETE_LOCAL_CONTENT:
				return new KObjectTaskDeleteLocalContentEngine();
			case ObjectTaskType::STORAGE_EXPORT:
				return new KObjectTaskStorageExportEngine();
			case ObjectTaskType::MODIFY_ENTRY:
				return new KObjectTaskModifyEntryEngine();
			default:
				return KalturaPluginManager::loadObject('KObjectTaskEntryEngineBase', $type);
		}
	}
} 