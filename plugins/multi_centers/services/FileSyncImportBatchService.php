<?php
/**
 * @service filesyncImportBatch
 * @package plugins.multiCenters
 * @subpackage api.services
 */
class FileSyncImportBatchService extends KalturaBatchService
{
	const MAX_FILESYNC_ID_PREFIX = 'fileSyncMaxId-dc';
	const LAST_FILESYNC_ID_PREFIX = 'fileSyncLastId-worker';
	const LOCK_KEY_PREFIX = 'fileSyncLock:id=';
	const LOCK_EXPIRY = 36000;
	
	/**
	 * Contain all object types and sub types that shouldn't be synced
	 * @var array
	 */
	static protected $excludedSyncFileObjectTypes = null;	
	
	/**
	 * Check if specific file sync that belong to object type and sub type should be synced
	 *
	 * @param int $objectType
	 * @param int $objectSubType
	 * @return bool
	 */
	public static function shouldSyncFileObjectType($fileSync)
	{
		if(is_null(self::$excludedSyncFileObjectTypes))
		{
			self::$excludedSyncFileObjectTypes = array();
			$dcConfig = kConf::getMap("dc_config");
			if(isset($dcConfig['sync_exclude_types']))
			{
				foreach($dcConfig['sync_exclude_types'] as $syncExcludeType)
				{
					$configObjectType = $syncExcludeType;
					$configObjectSubType = null;
						
					if(strpos($syncExcludeType, ':') > 0)
						list($configObjectType, $configObjectSubType) = explode(':', $syncExcludeType, 2);
	
					// translate api dynamic enum, such as contentDistribution.EntryDistribution - {plugin name}.{object name}
					if(!is_numeric($configObjectType))
						$configObjectType = kPluginableEnumsManager::apiToCore('FileSyncObjectType', $configObjectType);
						
					// translate api dynamic enum, including the enum type, such as conversionEngineType.mp4box.Mp4box - {enum class name}.{plugin name}.{object name}
					if(!is_null($configObjectSubType) && !is_numeric($configObjectSubType))
					{
						list($enumType, $configObjectSubType) = explode('.', $configObjectSubType);
						$configObjectSubType = kPluginableEnumsManager::apiToCore($enumType, $configObjectSubType);
					}
						
					if(!isset(self::$excludedSyncFileObjectTypes[$configObjectType]))
						self::$excludedSyncFileObjectTypes[$configObjectType] = array();
	
					if(!is_null($configObjectSubType))
						self::$excludedSyncFileObjectTypes[$configObjectType][] = $configObjectSubType;
				}
			}
		}
	
		if(!isset(self::$excludedSyncFileObjectTypes[$fileSync->getObjectType()]))
			return true;
			
		if(count(self::$excludedSyncFileObjectTypes[$fileSync->getObjectType()]) && 
			!in_array($fileSync->getObjectSubType(), self::$excludedSyncFileObjectTypes[$fileSync->getObjectType()]))
			return true;
			
		return false;
	}
	
	/**
	 * batch lockPendingFileSyncs action locks file syncs for import by the file sync periodic worker
	 *
	 * @action lockPendingFileSyncs
	 * @param KalturaFileSyncFilter $filter
	 * @param int $workerId The id of the file sync import worker 
	 * @param int $sourceDc The id of the DC from which the file syncs should be pulled
	 * @param int $maxCount The maximum number of file syncs that should be returned
	 * @param int $maxSize The maximum total size of file syncs that should be returned, this limit may be exceeded by one file sync
	 * @return KalturaLockFileSyncsResponse
	 */
	function lockPendingFileSyncsAction(KalturaFileSyncFilter $filter, $workerId, $sourceDc, $maxCount, $maxSize = null)
	{
		$keysCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_QUERY_CACHE_KEYS);
		if (!$keysCache)
		{
			throw new KalturaAPIException(MultiCentersErrors::GET_KEYS_CACHE_FAILED);
		}
		
		$maxId = $keysCache->get(self::MAX_FILESYNC_ID_PREFIX . $sourceDc);
		if (!$maxId)
		{
			throw new KalturaAPIException(MultiCentersErrors::GET_MAX_FILESYNC_ID_FAILED, $sourceDc);
		}
		
		$lastId = $keysCache->get(self::LAST_FILESYNC_ID_PREFIX . $workerId);
		if (!$lastId)
		{
			$lastId = $maxId;
		}
		
		$lockCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		if ($lockCache)
		{
			throw new KalturaAPIException(MultiCentersErrors::GET_LOCK_CACHE_FAILED);
		}
				
		// created at less than handled explicitly
		$createdAtLessThanOrEqual = $filter->createdAtLessThanOrEqual;
		$filter->createdAtLessThanOrEqual = null;
		
		// build the criteria
		$fileSyncFilter = new FileSyncFilter();
		$filter->toObject($fileSyncFilter);
		
		$c = new Criteria();
		$fileSyncFilter->attachToCriteria($c);
		
		$c->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_PENDING);
		$c->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_FILE);
		$c->add(FileSyncPeer::DC, kDataCenterMgr::getCurrentDcId());
		
		$c->addAscendingOrderByColumn(FileSyncPeer::ID);

		$c->setLimit(100);
		
		$lockedFileSyncs = array();
		$lockedFileSyncsSize = 0;
		$limitReached = false;
		$selectCount = 0;
		$done = false;
		
		while (!$done)
		{
			// clear the instance pool every once in a while (not clearing every time since 
			//	some objects repeat between selects)
			$selectCount++;
			if ($selectCount % 5 == 0)
			{
				FileSyncPeer::clearInstancePool();
			}
			
			// get a chunk of file syncs
			$idCriterion = $c->getNewCriterion(FileSyncPeer::ID, $lastId - 100, Criteria::GREATER_THAN);
			$idCriterion->addAnd($c->getNewCriterion(FileSyncPeer::ID, $maxId, Criteria::LESS_THAN));
			$c->addAnd($idCriterion);

			$fileSyncs = FileSyncPeer::doSelect($c);
			if (!$fileSyncs)
			{
				$lastId = $maxId;
				break;
			}
			
			$lastFileSync = end($fileSyncs);
			$lastId = $lastFileSync->getId();
			
			// filter by source dc
			foreach ($fileSyncs as $index => $fileSync)
			{
				if ($fileSync->getOriginalDc() != $sourceDc)
				{
					unset($fileSyncs[$index]);
				}
			}

			// filter by object type / sub type
			$fileSyncs = array_filter($fileSyncs, array('FileSyncImportBatchService', 'shouldSyncFileObjectType'));
			if (!$fileSyncs)
			{
				continue;
			}
			
			// filter by created at
			if ($createdAtLessThanOrEqual)
			{
				$firstFileSync = reset($fileSyncs);
				$lastId = $firstFileSync->getId();
				
				foreach ($fileSyncs as $index => $fileSync)
				{
					if ($fileSync->getCreatedAt(null) > $createdAtLessThanOrEqual)
					{
						$done = true;
						unset($fileSyncs[$index]);
					}
					else
					{
						$lastId = $fileSync->getId();
					}
				}
				
				if (!$fileSyncs)
				{
					break;
				}
			}
			
			// get locked file syncs with multi get
			$lockKeys = array();
			foreach ($fileSyncs as $fileSync)
			{
				$lockKeys[] = self::LOCK_KEY_PREFIX . $fileSync->getId();
			}
			
			$lockKeys = $lockCache->get($lockKeys);
			
			// try to lock file syncs
			foreach ($fileSyncs as $fileSync)
			{
				$curKey = self::LOCK_KEY_PREFIX . $fileSync->getId();
				if (isset($lockKeys[$curKey]))
				{
					continue;
				}
				
				if (!$lockCache->add($curKey, true, self::LOCK_EXPIRY))
				{
					continue;
				}
				
				// locked, add to the result set
				$lockedFileSyncs[] = $fileSync;
				$lockedFileSyncsSize += $fileSync->getFileSize();
				
				if (count($lockedFileSyncs) >= $maxCount ||
					($maxSize && $lockedFileSyncsSize >= $maxSize))
				{
					$lastId = $fileSync->getId();
					$limitReached = true;
					break;
				}
			}
			
			if ($limitReached)
			{
				break;
			}
		}
		
		// update the last id
		// Note: it is possible that the last id will go back in case of race condition,
		//		but the only effect of this is that some file syncs will be scanned again 
		$keysCache->set(self::LAST_FILESYNC_ID_PREFIX . $workerId, $lastId);
		
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
		
		// build the response object
		$sourceDc = kDataCenterMgr::getDcById($sourceDc);
		$result = new KalturaLockFileSyncsResponse;
		$result->fileSyncs = KalturaFileSyncArray::fromDbArray($lockedFileSyncs, $this->getResponseProfile());
		$result->limitReached = $limitReached;
		$result->dcSecret = $sourceDc["secret"];
		$result->baseUrl = isset($sourceDc["fileSyncImportUrl"]) ? $sourceDc["fileSyncImportUrl"] : $sourceDc["url"];
		
		return $result;
	}	
}
