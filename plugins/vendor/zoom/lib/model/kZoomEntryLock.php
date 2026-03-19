<?php
/**
 * Shared Zoom entry locking utility
 */
require_once(dirname(__FILE__) . '/../../../../../../alpha/apps/kaltura/lib/cache/kCacheManager.php');
require_once(dirname(__FILE__) . '/../../../../../../alpha/apps/kaltura/lib/logging/KalturaLog.php');

class kZoomEntryLock
{
	const LOCK_HOLD_TIMEOUT = 30; // seconds

	/**
	 * Create and acquire lock for Zoom entry creation
	 * @param string $uuid
	 * @param string $recordingStartTime
	 * @return object|null Lock object with store/key properties or null if lock acquisition failed
	 */
	public static function acquireLock($uuid, string $recordingStartTime): ?object
	{

		$lockKey = "zoom_entry_creation_{$uuid}_{$recordingStartTime}";

		// Get distributed cache store (memcached/redis)
		$store = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		if (!$store)
		{
			KalturaLog::err("Could not get cache store for locking Zoom entry creation for UUID: {$uuid}");
			return null;
		}

		// Use store directly for locking (similar to kClipManager pattern)
		if (!$store->add($lockKey, true, self::LOCK_HOLD_TIMEOUT))
		{
			KalturaLog::debug("Could not acquire lock for {$lockKey}, another system is creating the entry");
			return null; // Lock acquisition failed
		}

		// Create a simple lock wrapper for the store
		return (object)['store' => $store, 'key' => $lockKey];   // Lock acquired
	}

	public static function unlock($lock): bool
	{
		return $lock->store->delete($lock->key);
	}
}
