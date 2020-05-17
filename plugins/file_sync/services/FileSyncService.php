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
	const LAST_FILESYNC_ID_PREFIX = 'fileSync-fileSyncLastId-worker-';
	const MAX_FILESYNCS_PER_CHUNK = 100;

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
	 * @return KalturaFileSyncListResponse
	 */
	function deleteLocalFileSyncsAction(KalturaFileSyncFilter $filter, $workerId)
	{
		//Get Cache
		$keysCache = self::getCache();

		// Last Id
		$lastId = $keysCache->get(self::LAST_FILESYNC_ID_PREFIX . $workerId);
		if(!$lastId)
		{
			$lastId = 0;
		}
		KalturaLog::info("Last ID is [{$lastId}]");

		// Get file syncs
		$fileSyncs = self::getFileSyncsChunk($filter, $lastId);

		if($fileSyncs)
		{
			// Delete siblings
			foreach ($fileSyncs as $fileSync)
			{
				KalturaLog::info("Delete siblings for file sync [{$fileSync->getObjectId()}] with ID [{$fileSync->getId()}]");

				$fileSYncKey = kFileSyncUtils::getKeyForFileSync($fileSync);
				kFileSyncUtils::deleteSyncFileForKey($fileSYncKey, false, true);
			}

			// Update last ID
			$lastFileSync = end($fileSyncs);
			$lastId = $lastFileSync->getId();

			KalturaLog::info("Set last ID to [{$lastId}]");
			$keysCache->set(self::LAST_FILESYNC_ID_PREFIX . $workerId, $lastId);
		}

		// Response
		$response = new KalturaFileSyncListResponse();
		$response->objects = KalturaFileSyncArray::fromDbArray($fileSyncs, $this->getResponseProfile());
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

	protected static function getFileSyncsChunk($filter, $lastId)
	{
		// build the criteria
		$fileSyncFilter = new FileSyncFilter();
		$filter->toObject($fileSyncFilter);

		$baseCriteria = new Criteria();
		$fileSyncFilter->attachToCriteria($baseCriteria);

		$baseCriteria->add(FileSyncPeer::ID, $lastId, Criteria::GREATER_THAN);
		$baseCriteria->add(FileSyncPeer::LINKED_ID, NULL, Criteria::ISNULL);

		$baseCriteria->addAscendingOrderByColumn(FileSyncPeer::ID);
		$baseCriteria->setLimit(self::MAX_FILESYNCS_PER_CHUNK);

		return FileSyncPeer::doSelect($baseCriteria, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
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

		$maxSize = PHP_INT_MAX;
		$lockedFileSyncs = array();
		$limitReached = false;

		// Get file syncs
		$fileSyncs = FileSync::getFileSyncsChunkNoCriteria($baseCriteria);

		if ($fileSyncs)
		{
			FileSync::lockFileSyncs($fileSyncs, $lockCache, self::LOCK_KEY_PREFIX, $lockExpiryTimeOut,
				$maxCount, $maxSize, $lockedFileSyncs, $limitReached);

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
