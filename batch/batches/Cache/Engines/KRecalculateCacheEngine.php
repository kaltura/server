<?php
/**
 * @package Scheduler
 * @subpackage RecalculateCache
 */
abstract class KRecalculateCacheEngine
{
	/**
	 * @param int $objectType of enum KalturaRecalculateCacheType
	 * @return KRecalculateCacheEngine
	 */
	public static function getInstance($objectType)
	{
		switch($objectType)
		{
			case KalturaRecalculateCacheType::RESPONSE_PROFILE:
				return new KRecalculateResponseProfileCacheEngine();
				
			default:
				return KalturaPluginManager::loadObject('KRecalculateCacheEngine', $objectType);
		}
	}
	
	/**
	 * @param KalturaRecalculateCacheJobData $data
	 * @return int cached objects count
	 */
	abstract public function recalculate(KalturaRecalculateCacheJobData $data);
}
