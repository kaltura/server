<?php
/**
 * The Storage Profile service allows you to export your Kaltura content to external storage volumes.
 * This service is disabled by default, please contact your account manager if you wish to enable it for your partner.
 *
 * @service storageProfile
 * @package api
 * @subpackage services
 */
class StorageProfileService extends KalturaBaseService
{
	const MAX_FILESYNC_ID_PREFIX = 'lastCreatedFileSyncId-dc';
	const LAST_FILESYNC_ID_PREFIX = 'storage-fileSyncLastId-worker';
	const LOCK_KEY_PREFIX = 'storage-fileSyncLock:id=';
	const STORAGE_LOCK_EXPIRY = 'storage_lock_expiry';
	const LAST_ID_LOOP_ADDITION = 'last_id_loop_addition';
	const MAX_ID_DELAY = 'max_id_delay';
	const DEFAULT_LOCK_EXPIRY = 36000;
	const MAX_FILESYNC_QUERIES_PER_CALL = 100;
	const MAX_FILESYNC_ID_RANGE = 20000;
	const DEFAULT_MAX_ID_DELAY = 1000;
	const DEFAULT_LAST_ID_LOOP_ADDITION = 100;

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		$partnerId = $this->getPartnerId();
		if(!$this->getPartner()->getEnabledService(PermissionName::FEATURE_REMOTE_STORAGE))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
			
		$this->applyPartnerFilterForClass('StorageProfile');
	}

	/**
	 * Adds a storage profile to the Kaltura DB.
	 *
	 * @action add
	 * @param KalturaStorageProfile $storageProfile 
	 * @return KalturaStorageProfile
	 */
	function addAction(KalturaStorageProfile $storageProfile)
	{
		if(!$storageProfile->status)
			$storageProfile->status = KalturaStorageProfileStatus::DISABLED;
			
		$dbStorageProfile = $storageProfile->toInsertableObject();
		/* @var $dbStorageProfile StorageProfile */
		$dbStorageProfile->setPartnerId($this->impersonatedPartnerId);
		$dbStorageProfile->save();
		
		$storageProfile = KalturaStorageProfile::getInstanceByType($dbStorageProfile->getProtocol());
				
		$storageProfile->fromObject($dbStorageProfile, $this->getResponseProfile());
		return $storageProfile;
	}
		
	/**
	 * @action updateStatus
	 * @param int $storageId
	 * @param KalturaStorageProfileStatus $status
	 */
	public function updateStatusAction($storageId, $status)
	{
		$dbStorage = StorageProfilePeer::retrieveByPK($storageId);
		if (!$dbStorage)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $storageId);
			
		$dbStorage->setStatus($status);
		$dbStorage->save();
	}	
	
	/**
	 * Get storage profile by id
	 * 
	 * @action get
	 * @param int $storageProfileId
	 * @return KalturaStorageProfile
	 */
	function getAction($storageProfileId)
	{
		$dbStorageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
		if (!$dbStorageProfile)
			return null;

		$protocol = $dbStorageProfile->getProtocol();
		$storageProfile = KalturaStorageProfile::getInstanceByType($protocol);
		
		$storageProfile->fromObject($dbStorageProfile, $this->getResponseProfile());
		return $storageProfile;
	}
	
	/**
	 * Update storage profile by id 
	 * 
	 * @action update
	 * @param int $storageProfileId
	 * @param KalturaStorageProfile $storageProfile
	 * @return KalturaStorageProfile
	 */
	function updateAction($storageProfileId, KalturaStorageProfile $storageProfile)
	{
		$dbStorageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
		if (!$dbStorageProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $storageProfileId);
			
		$dbStorageProfile = $storageProfile->toUpdatableObject($dbStorageProfile);
		$dbStorageProfile->save();
		
		$protocol = $dbStorageProfile->getProtocol();
		$storageProfile = KalturaStorageProfile::getInstanceByType($protocol);
		
		$storageProfile->fromObject($dbStorageProfile, $this->getResponseProfile());
		return $storageProfile;
	}
	
	/**	
	 * @action list
	 * @param KalturaStorageProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaStorageProfileListResponse
	 */
	public function listAction(KalturaStorageProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$c = new Criteria();
		
		if (!$filter)
			$filter = new KalturaStorageProfileFilter();
		
		$storageProfileFilter = new StorageProfileFilter();
		$filter->toObject($storageProfileFilter);
		$storageProfileFilter->attachToCriteria($c);
		$list = StorageProfilePeer::doSelect($c);
			
		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$pager->attachToCriteria($c);
		
		$response = new KalturaStorageProfileListResponse();
		$response->totalCount = StorageProfilePeer::doCount($c);
		$response->objects = KalturaStorageProfileArray::fromDbArray($list, $this->getResponseProfile());
		return $response;
	}



	/**
	 * storage profile lockPendingFileSyncs action locks file syncs for export by the file sync periodic worker
	 *
	 * @action lockPendingFileSyncs
	 * @param KalturaFileSyncFilter $filter
	 * @param int $workerId The id of the file sync import worker
	 * @param int $storageProfileId The id of the storage profile
	 * @param int $maxCount The maximum number of file syncs that should be returned
	 * @param int $maxSize The maximum total size of file syncs that should be returned, this limit may be exceeded by one file sync
	 * @return KalturaLockFileSyncsResponse
	 */
	function lockPendingFileSyncsAction(KalturaFileSyncFilter $filter, $workerId, $storageProfileId, $maxCount, $maxSize = null)
	{
		// need to explicitly disable the cache since this action may not perform any queries
		kApiCache::disableConditionalCache();
		list($keysCache, $lockCache) = self::getCacheLayers();

		$cloudStorageConfig =  kConf::getMap('cloud_storage');
		$storageLockExpiry = self::getConfigVal($cloudStorageConfig, self::STORAGE_LOCK_EXPIRY, self::DEFAULT_LOCK_EXPIRY);
		$lastIdLoopAddition = self::getConfigVal($cloudStorageConfig, self::LAST_ID_LOOP_ADDITION, self::DEFAULT_LAST_ID_LOOP_ADDITION);
		$maxIdDelay = self::getConfigVal($cloudStorageConfig, self::MAX_ID_DELAY, self::DEFAULT_MAX_ID_DELAY);

		$maxId = self::getMaxId($keysCache, $storageProfileId, $maxIdDelay);
		KalturaLog::info("got maxId [$maxId] for worker [$workerId]");
		$initialLastId = $keysCache->get(self::LAST_FILESYNC_ID_PREFIX . $workerId);
		KalturaLog::info("got lastId [$initialLastId] for worker [$workerId]");
		$lastId = $initialLastId ? $initialLastId : $maxId;

		// created at less than handled explicitly
		$createdAtLessThanOrEqual = $filter->createdAtLessThanOrEqual;
		$filter->createdAtLessThanOrEqual = null;

		$baseCriteria = $filter->buildFileSyncNotLinkedCriteria();

		$lockedFileSyncs = array();
		$lockedFileSyncsSize = 0;
		$limitReached = false;
		$selectCount = 0;
		$done = false;

		KalturaLog::info("lastId [$lastId] maxId [$maxId]");
		while ( !$done && !$limitReached && ($selectCount < self::MAX_FILESYNC_QUERIES_PER_CALL) && ($lastId + $lastIdLoopAddition < $maxId) )
		{
			// clear the instance pool every once in a while (not clearing every time since some objects repeat between selects)
			$selectCount++;
			if ($selectCount % 5 == 0)
			{
				FileSyncPeer::clearInstancePool();
			}
			$idLimit = min($lastId + self::MAX_FILESYNC_ID_RANGE, $maxId);
			$fileSyncs = FileSync::getFileSyncsChunkNoCriteria($baseCriteria, $lastId, $idLimit);

			if (!$fileSyncs)
			{
				break;
			}

			if (count($fileSyncs) < KalturaFileSyncFilter::MAX_FILESYNCS_PER_CHUNK)
			{
				$done = true;
			}

			$lastId = end($fileSyncs)->getId();

			self::filterFileSyncs($fileSyncs, $lastId, $done, $createdAtLessThanOrEqual);

			$lockKeys = FileSync::getLockedFileSyncs($fileSyncs, $lockCache, self::LOCK_KEY_PREFIX);
			FileSync::lockFileSyncs($fileSyncs, $lockKeys, $lockCache, self::LOCK_KEY_PREFIX, $storageLockExpiry,
				$maxCount, $maxSize, $lockedFileSyncs, $limitReached, $lastId, $lockedFileSyncsSize);

			KalturaLog::debug("Update lastId to [$lastId]");
		}

		self::setLastIdInCache($initialLastId, $lastId, $keysCache, $workerId);
		FileSync::createFileSyncsPath($lockedFileSyncs);

		// build the response object
		$result = new KalturaLockFileSyncsResponse;
		$result->fileSyncs = KalturaFileSyncArray::fromDbArray($lockedFileSyncs, $this->getResponseProfile());
		$result->limitReached = $limitReached;

		return $result;

	}

	protected static function getCacheLayers()
	{
		// get caches
		$keysCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_QUERY_CACHE_KEYS);
		if (!$keysCache)
		{
			throw new KalturaAPIException(MultiCentersErrors::GET_KEYS_CACHE_FAILED);
		}

		$lockCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		if (!$lockCache)
		{
			throw new KalturaAPIException(MultiCentersErrors::GET_LOCK_CACHE_FAILED);
		}

		return array($keysCache, $lockCache);
	}

	protected static function getMaxId($keysCache, $storageProfileId, $maxIdDelay)
	{
		// get the max id / last id
		$maxId = $keysCache->get(self::MAX_FILESYNC_ID_PREFIX . $storageProfileId);
		if (!$maxId)
		{
			throw new KalturaAPIException(MultiCentersErrors::GET_MAX_FILESYNC_ID_FAILED, $storageProfileId);
		}
		$maxId -= $maxIdDelay;
		return $maxId;
	}

	protected static function filterFileSyncs(&$fileSyncs, &$lastId, &$done, $createdAtLessThanOrEqual)
	{
		// filter by created at
		if ($createdAtLessThanOrEqual)
		{
			foreach ($fileSyncs as $index => $fileSync)
			{
				if ($fileSync->getCreatedAt(null) > $createdAtLessThanOrEqual)
				{
					$done = true;
					unset($fileSyncs[$index]);
					$lastId = min($lastId, $fileSync->getId());
				}
			}
		}
	}

	protected static function setLastIdInCache($initialLastId, $lastId, $keysCache, $workerId)
	{
		// update the last id
		// Note: it is possible that the last id will go back in case of race condition,
		//		but the only effect of this is that some file syncs will be scanned again
		if (!$initialLastId || $lastId > $initialLastId)
		{
			KalturaLog::info("setting lastId to [$lastId] for worker [$workerId]");

			$keysCache->set(self::LAST_FILESYNC_ID_PREFIX . $workerId, $lastId);
		}
	}

	protected static function getConfigVal($configMap, $configField, $defaultVal)
	{
		if(isset($configMap[$configField]))
		{
			return $configMap[$configField];
		}
		return $defaultVal;
	}
}
