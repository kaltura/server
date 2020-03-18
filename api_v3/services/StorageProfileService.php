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
	const MAX_FILESYNC_ID_PREFIX = 'fileSyncMaxId-dc';
	const LAST_FILESYNC_ID_PREFIX = 'storage-fileSyncLastId-worker';
	const LOCK_KEY_PREFIX = 'storage-fileSyncLock:id=';
	const LOCK_EXPIRY = 36000;
	const MAX_FILESYNCS_PER_CHUNK = 100;
	const MAX_FILESYNC_QUERIES_PER_CALL = 100;
	const MAX_FILESYNC_ID_RANGE = 20000;

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

		$maxId = self::getMaxId($keysCache, $storageProfileId);
		$initialLastId = $keysCache->get(self::LAST_FILESYNC_ID_PREFIX . $workerId);
		KalturaLog::info("got lastId [$initialLastId] for worker [$workerId]");
		$lastId = $initialLastId ? $initialLastId : $maxId;

		// created at less than handled explicitly
		$createdAtLessThanOrEqual = $filter->createdAtLessThanOrEqual;
		$filter->createdAtLessThanOrEqual = null;

		$baseCriteria = self::buildFileSyncCriteria($filter, $storageProfileId);

		$lockedFileSyncs = array();
		$lockedFileSyncsSize = 0;
		$limitReached = false;
		$selectCount = 0;
		$done = false;

		KalturaLog::info("lastId [$lastId] maxId [$maxId]");
		while (!$done && $selectCount < self::MAX_FILESYNC_QUERIES_PER_CALL && $lastId + 100 < $maxId)
		{
			// clear the instance pool every once in a while (not clearing every time since some objects repeat between selects)
			$selectCount++;
			if ($selectCount % 5 == 0)
			{
				FileSyncPeer::clearInstancePool();
			}
			$idLimit = min($lastId + self::MAX_FILESYNC_ID_RANGE, $maxId);
			$fileSyncs = self::getFileSyncsChunk($baseCriteria, $lastId, $idLimit);

			$lastId = self::updateLastId($fileSyncs, $idLimit);
			self::filterFileSyncs($fileSyncs, $lastId, $done, $createdAtLessThanOrEqual);

			if (!$fileSyncs)
			{
				continue;
			}

			$lockKeys = self::getLockedFileSyncs($fileSyncs, $lockCache);
			self::lockFileSyncs($fileSyncs, $lockKeys, $lockCache, $lockedFileSyncs, $lockedFileSyncsSize, $maxCount, $maxSize, $lastId, $limitReached, $done);
		}

		self::setLastIdInCache($initialLastId, $lastId, $keysCache, $workerId);
		self::fillMissingFileSyncsPath($lockedFileSyncs);

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

	protected static function getMaxId($keysCache, $storageProfileId)
	{
		// get the max id / last id
		$maxId = $keysCache->get(self::MAX_FILESYNC_ID_PREFIX . $storageProfileId);
		if (!$maxId)
		{
			throw new KalturaAPIException(MultiCentersErrors::GET_MAX_FILESYNC_ID_FAILED, $storageProfileId);
		}
		$maxId -= 1000;
		return $maxId;
	}

	protected static function buildFileSyncCriteria($filter, $storageProfileId)
	{
		// build the criteria
		$fileSyncFilter = new FileSyncFilter();
		$filter->toObject($fileSyncFilter);

		$baseCriteria = new Criteria();
		$fileSyncFilter->attachToCriteria($baseCriteria);

		$baseCriteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_PENDING);
		$baseCriteria->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_URL);
		$baseCriteria->add(FileSyncPeer::DC, $storageProfileId, Criteria::IN);

		$baseCriteria->addAscendingOrderByColumn(FileSyncPeer::ID);
		$baseCriteria->setLimit(self::MAX_FILESYNCS_PER_CHUNK);

		return $baseCriteria;
	}

	protected static function getFileSyncsChunk($baseCriteria, $lastId, $idLimit)
	{
		// get a chunk of file syncs
		// Note: starting slightly before the last id, because the ids may arrive out of order in the mysql replication
		$c = clone $baseCriteria;
		$idCriterion = $c->getNewCriterion(FileSyncPeer::ID, $lastId, Criteria::GREATER_EQUAL);
		$idCriterion->addAnd($c->getNewCriterion(FileSyncPeer::ID, $idLimit, Criteria::LESS_THAN));
		$c->addAnd($idCriterion);

		// Note: disabling the criteria because it accumulates more and more criterions, and the status was already explicitly added
		//		once that bug is fixed, this can be removed
		FileSyncPeer::setUseCriteriaFilter(false);
		$fileSyncs = FileSyncPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		FileSyncPeer::setUseCriteriaFilter(true);

		return $fileSyncs;
	}

	protected static function updateLastId($fileSyncs, $idLimit)
	{
		// if we got less than the limit no reason to perform any more queries
		if (count($fileSyncs) < self::MAX_FILESYNCS_PER_CHUNK)
		{
			$lastId = $idLimit;
		}
		else
		{
			$lastFileSync = end($fileSyncs);
			$lastId = $lastFileSync->getId() + 1;
		}
		return $lastId;
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

	protected static function getLockedFileSyncs($fileSyncs, $lockCache)
	{
		// get locked file syncs with multi get
		$lockKeys = array();
		foreach ($fileSyncs as $fileSync)
		{
			$lockKeys[] = self::LOCK_KEY_PREFIX . $fileSync->getId();
		}

		$lockKeys = $lockCache->get($lockKeys);
		return $lockKeys;
	}

	protected static function lockFileSyncs($fileSyncs, $lockKeys, $lockCache, &$lockedFileSyncs, &$lockedFileSyncsSize, $maxCount, $maxSize, &$lastId, &$limitReached, &$done)
	{
		// try to lock file syncs
		foreach ($fileSyncs as $fileSync)
		{
			$curKey = self::LOCK_KEY_PREFIX . $fileSync->getId();

			if (isset($lockKeys[$curKey]))
			{
				KalturaLog::info('file sync '.$fileSync->getId().' already locked');
				continue;
			}

			if (!$lockCache->add($curKey, true, self::LOCK_EXPIRY))
			{
				KalturaLog::info('failed to lock file sync '.$fileSync->getId());
				continue;
			}

			KalturaLog::info('locked file sync ' . $fileSync->getId());

			// add to the result set
			$lockedFileSyncs[] = $fileSync;
			$lockedFileSyncsSize += $fileSync->getFileSize();

			if (count($lockedFileSyncs) >= $maxCount ||
				($maxSize && $lockedFileSyncsSize >= $maxSize))
			{
				$lastId = min($lastId, $fileSync->getId() + 1);
				$limitReached = true;
				$done = true;
				break;
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

	protected static function fillMissingFileSyncsPath(&$lockedFileSyncs)
	{
		// make sure all file syncs have a path
		foreach ($lockedFileSyncs as $fileSync)
		{
			if ($fileSync->getFileRoot() && $fileSync->getFilePath())
			{
				continue;
			}

			$fileSyncKey = kFileSyncUtils::getKeyForFileSync($fileSync);
			list($fileRoot, $realPath) = kPathManager::getFilePathArr($fileSyncKey);

			$fileSync->setFileRoot($fileRoot);
			$fileSync->setFilePath($realPath);
		}
	}
}
