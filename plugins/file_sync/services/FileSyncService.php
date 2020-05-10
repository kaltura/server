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
	const MAX_FILESYNC_QUERIES_PER_CALL = 100;
	const BIGINT_MAX = 9223372036854775807;

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

		$baseCriteria = $filter->buildFileSyncNotLinkedCriteria();

		$lockedFileSyncs = array();
		$limitReached = false;
		$selectCount = 0;
		$lastId = 0;

		$maxId = self::BIGINT_MAX;

		while ( !$limitReached && ($selectCount < self::MAX_FILESYNC_QUERIES_PER_CALL) && ($lastId < $maxId) )
		{
			// clear the instance pool every once in a while (not clearing every time since some objects repeat between selects)
			$selectCount++;
			if ($selectCount % 5 == 0)
			{
				FileSyncPeer::clearInstancePool();
			}

			$fileSyncs = FileSync::getFileSyncsChunkNoCriteria($baseCriteria, $lastId, $maxId);

			if (!$fileSyncs)
			{
				KalturaLog::debug('No file syncs to lock');
				break;
			}

			$lastId = FileSync::getLastFileSyncId($fileSyncs, $maxId);

			$lockKeys = FileSync::getLockedFileSyncs($fileSyncs, $lockCache, self::LOCK_KEY_PREFIX);

			FileSync::lockFileSyncs($fileSyncs, $lockKeys, $lockCache, self::LOCK_KEY_PREFIX, $lockExpiryTimeOut,
				$maxCount, 0, $lockedFileSyncs, $limitReached);
		}

		FileSync::createFileSyncsPath($lockedFileSyncs);

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
