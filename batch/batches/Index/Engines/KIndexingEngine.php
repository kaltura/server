<?php
/**
 * @package Scheduler
 * @subpackage Index
 */
abstract class KIndexingEngine
{
	/**
	 * @param int $objectType of enum KalturaIndexObjectType
	 * @return KIndexingEngine
	 */
	public static function getInstance($objectType)
	{
		switch($objectType)
		{
			case KalturaIndexObjectType::ENTRY:
				return new KIndexingEntryEngine();
				
			case KalturaIndexObjectType::CATEGORY:
				return new KIndexingCategoryEngine();
				
			default:
				return KalturaPluginManager::loadObject('KIndexingEngine', $objectType);
		}
	}
	
	abstract public function index(KalturaFilter $filter);
}
