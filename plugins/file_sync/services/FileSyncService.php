<?php
/**
 * System user service
 *
 * @service fileSync
 * @package plugins.fileSync
 * @subpackage api.services
 */
class FileSyncService extends KalturaBaseService
{
	const LOCK_KEY_PREFIX = 'fileSync_fileSyncLock:id=';
	const LOCK_KEY_PREFIX_DELETE_LOCAL = 'fileSync_fileSyncDeleteLocal:id=';
	const LAST_FILESYNC_UPDATE_AT_PREFIX = 'fileSync-fileSyncLastUpdatedAt-worker-';

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		// since plugin might be using KS impersonation, we need to validate the requesting
		// partnerId from the KS and not with the $_POST one
		if(!FileSyncPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, FileSyncPlugin::PLUGIN_NAME);
	}
	
	/**
	 * List file syce objects by filter and pager
	 *
	 * @action list
	 * @param KalturaFileSyncFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaFileSyncListResponse
	 */
	function listAction(KalturaFileSyncFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaFileSyncFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$fileSyncFilter = new FileSyncFilter();
		
		$filter->toObject($fileSyncFilter);

		$c = new Criteria();
		$fileSyncFilter->attachToCriteria($c);
		
		$totalCount = FileSyncPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = FileSyncPeer::doSelect($c);
		
		$list = KalturaFileSyncArray::fromDbArray($dbList, $this->getResponseProfile());
		$response = new KalturaFileSyncListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}

	/**
	 * Delete local file syncs by filter
	 *
	 * @action deleteLocalFileSyncs
	 * @param KalturaFileSyncFilter $filter
	 * @param int $workerId The id of the file sync import worker
	 * @param int $relativeTimeDeletionLimit Seconds from now that will be ignored
	 * @param int $relativeTimeRange Seconds of the query
	 * @param int $lockExpiryTimeout The expiry timeout of the lock
	 * @return KalturaFileSyncListResponse
	 */
	function deleteLocalFileSyncsAction(KalturaFileSyncFilter $filter, $workerId, $relativeTimeDeletionLimit, $relativeTimeRange, $lockExpiryTimeout)
	{
		// Get last updatedAt
		$keysCache = self::getCache();
		$lastUpdatedAt = $keysCache->get(self::LAST_FILESYNC_UPDATE_AT_PREFIX . $workerId);

		// Set range on filter
		self::setRange($filter, $lastUpdatedAt, $relativeTimeDeletionLimit, $relativeTimeRange);

		// Get and lock file syncs
		$lockedFileSyncs = self::getAndLockFileSyncs($filter, $lockExpiryTimeout);

		// Delete siblings
		foreach ($lockedFileSyncs as $fileSync)
		{
			$fileSync->deleteLocalSiblings();
		}

		// Set last updatedAt
		$keysCache->set(self::LAST_FILESYNC_UPDATE_AT_PREFIX . $workerId, $filter->updatedAtLessThanOrEqual);

		// Response
		$response = new KalturaFileSyncListResponse();
		$response->objects = KalturaFileSyncArray::fromDbArray($lockedFileSyncs, $this->getResponseProfile());
		return $response;
	}

	protected static function getCache()
	{
		$keysCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_QUERY_CACHE_KEYS);
		if (!$keysCache)
		{
			throw new KalturaAPIException(MultiCentersErrors::GET_KEYS_CACHE_FAILED);
		}
		return $keysCache;
	}

	protected static function setRange($filter, $lastUpdatedAt, $relativeTimeDeletionLimit, $relativeTimeRange)
	{
		$absoluteDeletionTimeLimit = time() - $relativeTimeDeletionLimit;

		if($lastUpdatedAt)
		{
			KalturaLog::info("Last updatedAt is [{$lastUpdatedAt}]");
			$filter->updatedAtGreaterThanOrEqual = $lastUpdatedAt + 1;
			$filter->updatedAtLessThanOrEqual = min($filter->updatedAtGreaterThanOrEqual + $relativeTimeRange, $absoluteDeletionTimeLimit);
		}
		else
		{
			KalturaLog::info('Use default updatedAt');
			$filter->updatedAtLessThanOrEqual = $absoluteDeletionTimeLimit;
			$filter->updatedAtGreaterThanOrEqual = $filter->updatedAtLessThanOrEqual - $relativeTimeRange;
		}

	}

	protected static function getAndLockFileSyncs($filter, $lockExpiryTimeout)
	{
		$lockCache = self::getLockCache();

		$baseCriteria = $filter->buildFileSyncNotLinkedCriteria(FileSyncPeer::UPDATED_AT);

		$offset = 0;
		$lockedFileSyncs = array();

		do
		{
			$baseCriteria->setOffset($offset);
			$fileSyncs = FileSync::getFileSyncsChunkNoCriteria($baseCriteria);
			if(!$fileSyncs)
			{
				break;
			}

			$offset += KalturaFileSyncFilter::MAX_FILESYNCS_PER_CHUNK;

			FileSync::lockFileSyncs($fileSyncs, $lockCache, self::LOCK_KEY_PREFIX_DELETE_LOCAL, $lockExpiryTimeout, $lockedFileSyncs);

		}while(count($fileSyncs) == KalturaFileSyncFilter::MAX_FILESYNCS_PER_CHUNK);

		return $lockedFileSyncs;
	}
	/**
	 * Update file sync by id
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaFileSync $fileSync
	 * @return KalturaFileSync
	 * 
	 * @throws FileSyncErrors::FILESYNC_ID_NOT_FOUND
	 */
	function updateAction($id, KalturaFileSync $fileSync)
	{
		$filterByStatus = ($fileSync->status !== null);

		if($filterByStatus)
		{
			FileSyncPeer::setUseCriteriaFilter(false);
		}

		$dbFileSync = FileSyncPeer::retrieveByPK($id);

		if($filterByStatus)
		{
			FileSyncPeer::setUseCriteriaFilter(true);
		}

		if (!$dbFileSync)
		{
			throw new KalturaAPIException(FileSyncErrors::FILESYNC_ID_NOT_FOUND, $id);
		}

		$fileSync->toUpdatableObject($dbFileSync);

		$dbFileSync->save();

		$dbFileSync->encrypt();

		$fileSync = new KalturaFileSync();

		$fileSync->fromObject($dbFileSync, $this->getResponseProfile());

		return $fileSync;
	}

	/**
	 * lockFileSyncs action locks file syncs for the file sync periodic worker
	 *
	 * @action lockFileSyncs
	 * @param KalturaFileSyncFilter $filter
	 * @param int $maxCount The maximum number of file syncs that should be returned
	 * @param int $lockExpiryTimeOut The expiry timeout of the lock
	 * @return KalturaLockFileSyncsResponse
	 */
	function lockFileSyncsAction(KalturaFileSyncFilter $filter, $maxCount, $lockExpiryTimeOut)
	{
		// need to explicitly disable the cache since this action may not perform any queries
		kApiCache::disableConditionalCache();

		$lockCache = self::getLockCache();

		$baseCriteria = $filter->buildFileSyncNotLinkedCriteria(FileSyncPeer::UPDATED_AT);

		$lockedFileSyncs = array();
		$limitReached = false;

		// Get file syncs
		$fileSyncs = FileSync::getFileSyncsChunkNoCriteria($baseCriteria);

		if ($fileSyncs)
		{
			FileSync::lockFileSyncs($fileSyncs, $lockCache, self::LOCK_KEY_PREFIX, $lockExpiryTimeOut,
				$lockedFileSyncs, $limitReached, $maxCount);

			FileSync::createFileSyncsPath($lockedFileSyncs);
		}

		// build the response object
		$result = new KalturaLockFileSyncsResponse;
		$result->fileSyncs = KalturaFileSyncArray::fromDbArray($lockedFileSyncs, $this->getResponseProfile());
		$result->limitReached = $limitReached;

		return $result;
	}

	protected static function getLockCache()
	{
		$lockCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		if (!$lockCache)
		{
			throw new KalturaAPIException(MultiCentersErrors::GET_LOCK_CACHE_FAILED);
		}

		return $lockCache;
	}
}
