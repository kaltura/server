<?php

/**
* @package Core
* @subpackage model
*/
abstract class kAllocator
{
	const TIME_IN_CACHE_FOR_LOCK = 5;
	
	/**
	 * @param string $objectName
	 * @param string $tag
	 * @param int $maxTimeForWatch
	 */
	public static function allocateObjectByTag($objectName, $tag, $maxTimeForWatch = 600)
	{
		KalturaLog::info("Allocating [$objectName] by tag [$tag]");
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_BATCH_JOBS);
		kApiCache::disableConditionalCache();
		if (!$cache)
		{
			KalturaLog::err("Cache layer [" . kCacheManager::CACHE_TYPE_BATCH_JOBS . "] not found, $objectName will not be allocated");
			return null;
		}
		
		$tagKey = self::getCacheKeyForObjectTag($objectName, $tag);
		$objectsToAllocate = $cache->get($tagKey);
		if (!$objectsToAllocate)
		{
			KalturaLog::debug("Could not find key [$tagKey] in cache");
			$objectsToAllocate = self::refreshObjectListFromDB($objectName, $cache, $tag);
			if (!$objectsToAllocate)
			{
				KalturaLog::debug("No results from database for allocating [$objectName]");
				return null;
			}
		}
		
		$indexKey = self::getCacheKeyForIndex($objectName, $tag);
		$allocatedObject = self::allocateObjectFromList($objectName, $cache, $objectsToAllocate, $indexKey,  $maxTimeForWatch);
		if (!$allocatedObject)
		{
			return null;
		}
		
		$objectId = $allocatedObject->getId();
		KalturaLog::debug("Allocated " . $objectName . " [$objectId] for [$maxTimeForWatch] seconds with tag [$tag]");
		return $allocatedObject;
	}
	
	/**
	 * Free object lock
	 * @param string $objectName
	 * @param string $objectId
	 */
	public static function unlockAllocatedObject($objectName, $objectId)
	{
		$lock = kLock::create(self::getLockKeyForObject($objectName, $objectId));
		$lock->unlock();
	}
	
	/**
	 * Try to lock the given object
	 * @param string $objectName
	 * @param string $objectId
	 * @param int $maxTimeForWatch
	 *
	 * @return boolean
	 */
	protected static function lockObject($objectName, $objectId, $maxTimeForWatch)
	{
		$lockKey = self::getLockKeyForObject($objectName, $objectId);
		$lock = kLock::create($lockKey);
		return $lock->lock(1, $maxTimeForWatch); // 1 is the lockGrabTimeout (seconds)
	}
	
	/**
	 * Return cache-key for tag
	 * @param string $objectName
	 * @param string $tag
	 * @return string
	 */
	protected static function getCacheKeyForObjectTag($objectName, $tag)
	{
		return $objectName . "_list_key_$tag";
	}
	
	/**
	 * Return cache-key for index by tag
	 * @param string $objectName
	 * @param string $tag
	 * @return string
	 */
	protected static function getCacheKeyForIndex($objectName, $tag)
	{
		return $objectName . "_list_$tag-index";
	}
	
	/**
	 * Return cache-key for lock by tag
	 * @param string $objectName
	 * @param string $tag
	 * @return string
	 */
	protected static function getCacheKeyForDBLock($objectName, $tag)
	{
		return $objectName . "update_$tag-Lock";
	}
	
	/**
	 * Return cache-key for lock by object id
	 * @param string $objectName
	 * @param int $objectId
	 * @return string
	 */
	protected static function getLockKeyForObject($objectName, $objectId)
	{
		return $objectName . "_lock_$objectId";
	}
	
	/**
	 * Insert bulk of objects to the cache from DB
	 * @param string $objectName
	 * @param kBaseCacheWrapper $cache
	 * @param string $tag
	 * @return array
	 */
	protected static function refreshObjectListFromDB($objectName, $cache, $tag)
	{
		if ($objectName == kScheduledProfileTaskAllocator::OBJECT_NAME)
		{
			return kScheduledProfileTaskAllocator::refreshObjectListFromDB($objectName, $cache, $tag);
		}
		elseif ($objectName == kDropFolderAllocator::OBJECT_NAME)
		{
			return kDropFolderAllocator::refreshObjectListFromDB($objectName, $cache, $tag);
		}
		return array();
	}
	
	/**
	 * Return the object from cache if exist
	 * @param string $objectName
	 * @param kBaseCacheWrapper $cache
	 * @param array $objectsList
	 * @param string $indexKey
	 * @param int $maxTimeForWatch
	 */
	protected static function allocateObjectFromList($objectName, $cache, $objectsList, $indexKey, $maxTimeForWatch)
	{
		if (!$objectsList)
		{
			return null;
		}
		
		$numOfObjects = count($objectsList);
		KalturaLog::debug("Retrieved list with $numOfObjects objects");
		for ($i = 0; $i < $numOfObjects; $i++)
		{
			$index = ($cache->increment($indexKey)) % $numOfObjects;
			$objectToAllocate = $objectsList[$index];
			if (!self::verifyAllocatedObject($objectName, $objectToAllocate))
			{
				continue;
			}
			if (self::lockObject($objectName, $objectToAllocate->getId(), $maxTimeForWatch))
			{
				return $objectToAllocate;
			}
		}
		KalturaLog::debug("Could not allocate any [$objectName] after [$numOfObjects] attempts");
		return null;
	}
	
	protected static function verifyAllocatedObject($objectName, $objectToAllocate)
	{
		if ($objectName == kScheduledProfileTaskAllocator::OBJECT_NAME)
		{
			return kScheduledProfileTaskAllocator::verifyAllocatedObject($objectName, $objectToAllocate);
		}
		return true;
	}
	
	protected static function refreshObjectsListInCache($cache, $objectName, $tag, $objectsFromDB, $ttlForList)
	{
		$tagKey = self::getCacheKeyForObjectTag($objectName, $tag);
		$indexKey = self::getCacheKeyForIndex($objectName, $tag);
		$indexKeyTtl = kTimeConversion::DAYS * 14;
		$numOfObjectsFromDB = count($objectsFromDB);
		KalturaLog::info("Inserted $numOfObjectsFromDB [$objectName] to cache with tag [$tag] for [$ttlForList] seconds");
		$cache->add($indexKey, 0, $indexKeyTtl);
		$cache->set($tagKey, $objectsFromDB, $ttlForList);
	}
}
