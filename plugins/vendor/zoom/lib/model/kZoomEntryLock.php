<?php
/**
 * Shared Zoom entry locking utility
 */
require_once(dirname(__FILE__) . '/../../../../../../alpha/apps/kaltura/lib/cache/kCacheManager.php');
require_once(dirname(__FILE__) . '/../../../../../../alpha/apps/kaltura/lib/locking/kLock.php');
require_once(dirname(__FILE__) . '/../../../../../../alpha/apps/kaltura/lib/logging/KalturaLog.php');

class kZoomEntryLock
{
	const LOCK_GRAB_TIMEOUT = 5; // seconds
	const LOCK_HOLD_TIMEOUT = 30; // seconds

	/**
	 * Create and acquire lock for Zoom entry creation
	 * @param string $uuid
	 * @param string $recordingStartTime
	 * @return array [kLock|null, bool $proceedWithoutLock]
	 */
	public static function acquireLock($uuid, $recordingStartTime)
	{
		if (empty($recordingStartTime))
		{
			throw new kCoreException("Recording start time is null or empty for UUID: {$uuid}");
		}

		$lockKey = "zoom_entry_creation_{$uuid}_{$recordingStartTime}";

		// Get distributed cache store (memcached/redis)
		$store = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		if (!$store)
		{
			KalturaLog::err("Could not get cache store for lock, proceeding without lock");
			return array(null, true); // proceedWithoutLock = true
		}

		// Use store directly for locking (similar to kClipManager pattern)
		if (!$store->add($lockKey, true, self::LOCK_HOLD_TIMEOUT))
		{
			KalturaLog::debug("Could not acquire lock for {$lockKey}, another system is creating the entry");
			return array(null, false); // proceedWithoutLock = false
		}

		// Create a simple lock wrapper for the store
		$lock = new kZoomSimpleLock($store, $lockKey);
		return array($lock, false); // proceedWithoutLock = false
	}
}

class kZoomSimpleLock
{
	private $store;
	private $key;

	public function __construct($store, $key)
	{
		$this->store = $store;
		$this->key = $key;
	}

	public function unlock()
	{
		return $this->store->delete($this->key);
	}
}
