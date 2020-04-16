<?php


	
chdir(__DIR__);
require_once(__DIR__ . '/../bootstrap.php');

// -------------------------------------------

class kOldContentCleaner
{
	/**
	 * Indicates that database will be readonly and update action will be logged but not executed.
	 * Indicates that files deletion will be logged but not executed.
	 *
	 * @var boolean
	 */
	protected static $dryRun = true;
	
	/**
	 * Queries records limit
	 * @var int
	 */
	protected static $queryLimit = 1000;
	
	/**
	 * The updated at time to start search for file syncs of old versions
	 * @var int
	 */
	protected static $oldVersionsStartUpdatedAt = array();
	
	/**
	 * The updated at time to end search for file syncs of old versions
	 * @var int
	 */
	protected static $oldVersionsEndUpdatedAt = array();
	
	/**
	 * The updated at time to start search for file syncs of old versions on the next execution
	 * @var int
	 */
	protected static $oldVersionsNextStartUpdatedAt = array();
	
	/**
	 * The updated at time to start search for file syncs to purge
	 * @var int
	 */
	protected static $purgeStartUpdatedAt = null;
	
	/**
	 * The updated at time to end search for file syncs to purge
	 * @var int
	 */
	protected static $purgeEndUpdatedAt = null;
	
	/**
	 * The updated at time to start search for file syncs to purge on the next execution
	 * @var int
	 */
	protected static $purgeNextStartUpdatedAt = null;
	
	/**
	 * Blocked partners file syncs months to delete
	 * @var int
	 */
	protected static $oldPartnersUpdatedAt = null;
	
	/**
	 * Error objects file syncs months to delete
	 * @var int
	 */
	protected static $errObjectsUpdatedAt = null;
	
	/**
	 * Array of final summaries
	 * @var array
	 */
	protected static $sums = array();
	
	protected static $deleteDeletedPartnersFileSyncs = false;
	protected static $deleteErrorObjects = false;
	protected static $deleteOldVersions = false;
	protected static $purgeDeletedFileSyncs = false;
		
	/**
	 * List of object type that their old versions could be deleted
	 * @var array
	 */
	protected static $objectsToClean = array(
		FileSyncObjectType::ASSET => array(
			asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET,
		),
		FileSyncObjectType::UICONF => array(
			uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA,
			uiConf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES,
		),
		FileSyncObjectType::ENTRY => array(
			kEntryFileSyncSubType::THUMB,
			kEntryFileSyncSubType::DATA,
			kEntryFileSyncSubType::DOWNLOAD,
		),
		FileSyncObjectType::METADATA => array(
			Metadata::FILE_SYNC_METADATA_DATA,
		),
		FileSyncObjectType::METADATA_PROFILE => array(
			MetadataProfile::FILE_SYNC_METADATA_DEFINITION,
			MetadataProfile::FILE_SYNC_METADATA_VIEWS,
		),
	);
	
	protected static function init()
	{
		kEventsManager::enableDeferredEvents(false);
		
		MetadataProfilePeer::setUseCriteriaFilter(false);
		MetadataPeer::setUseCriteriaFilter(false);
		entryPeer::setUseCriteriaFilter(false);
		uiConfPeer::setUseCriteriaFilter(false);
		assetPeer::setUseCriteriaFilter(false);
		PartnerPeer::setUseCriteriaFilter(false);
		FileSyncPeer::setUseCriteriaFilter(false);
		
		$options = getopt('hrl:p:o:b:e:', array(
			'real-run',
			'error-objects',
			'old-versions',
			'blocked-partners',
			'files',
		));
	
		if(isset($options['h']))
			self::failWrongInputs();
		
		if(isset($options['blocked-partners']))
			self::$deleteDeletedPartnersFileSyncs = true;
		if(isset($options['error-objects']))
			self::$deleteErrorObjects = true;
		if(isset($options['old-versions']))
			self::$deleteOldVersions = true;
		if(isset($options['files']))
			self::$purgeDeletedFileSyncs = true;
		
		if(isset($options['r']) || isset($options['real-run']))
			self::$dryRun = false;
			
		KalturaStatement::setDryRun(self::$dryRun);
			
		$cacheFilePath = kConf::get('cache_root_path') . '/scripts/deleteOldContent.cache';
		if(file_exists($cacheFilePath))
		{
			$cache = unserialize(file_get_contents($cacheFilePath));
			if(isset($cache['oldVersionsStartUpdatedAt']))
				self::$oldVersionsStartUpdatedAt = $cache['oldVersionsStartUpdatedAt'];
			if(isset($cache['purgeStartUpdatedAt']))
				self::$purgeStartUpdatedAt = $cache['purgeStartUpdatedAt'];
		}
		
		if(!self::$purgeStartUpdatedAt)
		{
			$criteria = new Criteria();
			$criteria->add(FileSyncPeer::UPDATED_AT, 0, Criteria::GREATER_THAN);
			$criteria->add(FileSyncPeer::DC, kDataCenterMgr::getCurrentDcId());
			$criteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_DELETED);
			$criteria->addSelectColumn('UNIX_TIMESTAMP(MIN(' . FileSyncPeer::UPDATED_AT . '))');
			$stmt = FileSyncPeer::doSelectStmt($criteria);
			$mins = $stmt->fetchAll(PDO::FETCH_COLUMN);
			if(count($mins))
				self::$purgeStartUpdatedAt = reset($mins);
		}
		if(is_null(self::$purgeStartUpdatedAt))
			self::$purgeStartUpdatedAt = 0;
			
		self::$purgeNextStartUpdatedAt = self::$purgeStartUpdatedAt;
		
		$oldVersionsUpdatedAtPeriod = 30; // days
		if(isset($options['o']))
		{
			if(!is_numeric($options['o']) || $options['o'] < 0)
				self::failWrongInputs("Period of old versions to delete must be positive numeric of days");
				
			$oldVersionsUpdatedAtPeriod = $options['o'];
		}
		foreach(self::$oldVersionsStartUpdatedAt as $objectType => $oldVersionsStartUpdatedAt)
			self::$oldVersionsEndUpdatedAt[$objectType] = $oldVersionsStartUpdatedAt + ($oldVersionsUpdatedAtPeriod * 60 * 60 * 24); // days
		
		$purgeUpdatedAtPeriod = 30; // days
		if(isset($options['p']))
		{
			if(!is_numeric($options['p']) || $options['p'] < 0)
				self::failWrongInputs("Period of purge must be positive numeric of days");
				
			$purgeUpdatedAtPeriod = $options['p'];
		}
		self::$purgeEndUpdatedAt = self::$purgeStartUpdatedAt + ($purgeUpdatedAtPeriod * 60 * 60 * 24); // days
		
		$oldPartnersUpdatedAtPeriod = 24; // months
		if(isset($options['b']))
		{
			if(!is_numeric($options['b']) || $options['b'] < 0)
				self::failWrongInputs("Period of blocked partners to delete must be positive numeric of months");
				
			$oldPartnersUpdatedAtPeriod = $options['b'];
		}
		self::$oldPartnersUpdatedAt = time() - ($oldPartnersUpdatedAtPeriod * 60 * 60 * 24 * 30); // months
		
		$errObjectsUpdatedAtPeriod = 24; // months
		if(isset($options['e']))
		{
			if(!is_numeric($options['e']) || $options['e'] < 0)
				self::failWrongInputs("Period of error objects to delete must be positive numeric of months");
				
			$errObjectsUpdatedAtPeriod = $options['e'];
		}
		self::$errObjectsUpdatedAt = time() - ($errObjectsUpdatedAtPeriod * 60 * 60 * 24 * 30); // months
		
		if(isset($options['l']))
		{
			if(!is_numeric($options['l']) || $options['l'] < 0)
				self::failWrongInputs("Limit querymust be positive numeric value");
				
			self::$queryLimit = $options['l'];
		}
	}
	
	protected static function finit()
	{
		$cache = array(
			'oldVersionsStartUpdatedAt' => self::$oldVersionsNextStartUpdatedAt,
			'purgeStartUpdatedAt' => self::$purgeNextStartUpdatedAt,
		);
	
		$cacheFilePath = kConf::get('cache_root_path') . '/scripts/deleteOldContent.cache';
		file_put_contents($cacheFilePath, serialize($cache));
		
		if(isset(self::$sums['entry']))
			KalturaLog::info('Deleted ' . self::$sums['entry'] . ' entries.');
		if(isset(self::$sums['asset']))
			KalturaLog::info('Deleted ' . self::$sums['asset'] . ' assets.');
		if(isset(self::$sums['FileSync']))
			KalturaLog::info('Deleted ' . self::$sums['FileSync'] . ' file sync objects.');
		if(isset(self::$sums['dirs']))
			KalturaLog::info('Deleted ' . self::$sums['dirs'] . ' directories.');
		if(isset(self::$sums['files']))
		{
			if(isset(self::$sums['bytes']))
			{
				$size = self::$sums['bytes'];
				$units = 'bytes';
				if($size > 1024)
				{
					$size = round($size / 1024, 2);
					$units = 'KB';
				}
				if($size > 1024)
				{
					$size = round($size / 1024, 2);
					$units = 'MB';
				}
				if($size > 1024)
				{
					$size = round($size / 1024, 2);
					$units = 'GB';
				}
				if($size > 1024)
				{
					$size = round($size / 1024, 2);
					$units = 'TB';
				}
				KalturaLog::info("Deleted " . self::$sums['files'] . " files in total size of $size $units from the disc.");
			}
			else
			{
				KalturaLog::info('Deleted ' . self::$sums['files'] . ' files.');
			}
		}
	}
	
	protected static function failWrongInputs($message = null)
	{
		if($message)
			echo "\n$message\n";
			
		echo "\n";
		echo "Usage: php " . __FILE__ . " [options]\n";
		echo "By default the script runs in dry run mode, meaning, the files are not deleted and the database is not affected.\n\n";
		echo "Options:\n";
		echo "\t-h Show this help.\n";
		echo "\t-r / real-run: Real run, commit database updates and file deletions.\n";
		echo "\t-l: Limit queries records count, default is 1000.\n";
		echo "\t-p: Purge file syncs days to delete, default is 30.\n";
		echo "\t-o: Old versions of existing file syncs days to delete, default is 30.\n";
		echo "\t-b: Blocked partners file syncs months to delete, default is 24.\n";
		echo "\t-e: Error objects file syncs months to delete, default is 24.\n";
		echo "\t--error-objects: Delete objects in error status that are also older than 30 days or as configured by -e option.\n";
		echo "\t--old-versions: Delete file sync objects of old versions that are also older than 30 days or as configured by -o option.\n";
		echo "\t--blocked-partners: Delete file sync objects of old blocked or deleted partners that are also older than 24 months or as configured by -b option.\n";
		echo "\t--files: Delete files from the disc according to file sync objects that marked as deleted, the file sync objects will be marked as purged after the physical deletion from the disc.\n";
		
		
		exit(-1);
	}
	
	protected static function incrementSummary($type, $amount = 1)
	{
		if(!isset(self::$sums[$type]))
			self::$sums[$type] = floatval(0);
			
		self::$sums[$type] += floatval($amount);
	}
	
	public static function clean()
	{
		$time = time();
		
		self::init();
		
		if(self::$deleteDeletedPartnersFileSyncs)
			self::deleteDeletedPartnersFileSyncs();
		if(self::$deleteErrorObjects)
			self::deleteErrorObjects();
		if(self::$deleteOldVersions)
			self::deleteOldVersions();
		if(self::$purgeDeletedFileSyncs)
			self::purgeDeletedFileSyncs();
		
		self::finit();
		
		KalturaLog::debug('Done, execution time ' . date('H:i:s', time() - $time) . '.');
	}
	
	/**
	 * @param FileSync $fileSync
	 */
	protected static function deleteFileSync(FileSync $fileSync)
	{
		KalturaLog::info("Deleting file sync [" . $fileSync->getId() . "]");
		$key = kFileSyncUtils::getKeyForFileSync($fileSync);
	
		try
		{
			kFileSyncUtils::deleteSyncFileForKey($key);
		}
		catch (Exception $e)
		{
			KalturaLog::err($e);
		}
		
		self::incrementSummary('FileSync');
	}
	
	/**
	 * @param FileSync $fileSync
	 */
	protected static function purgeDeletedFileSyncs()
	{
		$deletedFileSyncs = self::getDeletedFileSyncs();
		foreach ($deletedFileSyncs as $fileSync)
			self::purgeFileSync($fileSync);
		kMemoryManager::clearMemory();
	}
	
	/**
	 * @param FileSync $fileSync
	 */
	protected static function purgeFileSync(FileSync $fileSync)
	{
		KalturaLog::info("Purging file sync [" . $fileSync->getId() . "]");
		
		$fullPath = $fileSync->getFullPath();
		if($fullPath && file_exists($fullPath))
		{
			KalturaLog::debug("Purging file sync [" . $fileSync->getId() . "] path [$fullPath]");
			if(is_dir($fullPath))
			{
				$command = "rm -fr $fullPath";
				KalturaLog::debug("Executing: $command");
				if(self::$dryRun)
				{
					self::incrementSummary('dirs');
				}
				else
				{
					$returnedValue = null;
					passthru($command, $returnedValue);
					if($returnedValue === 0)
					{
						self::incrementSummary('dirs');
					}
					else
					{
						KalturaLog::err("Failed purging file sync [" . $fileSync->getId() . "] directory path [$fullPath]");
						return;
					}
				}
			}
			else
			{
				$fileSize = filesize($fullPath);
				if(self::$dryRun || unlink($fullPath))
				{
					self::incrementSummary('bytes', $fileSize);
					self::incrementSummary('files');
				}
				else
				{
					KalturaLog::err("Failed purging file sync [" . $fileSync->getId() . "] file path [$fullPath]");
					return;
				}
			}
		}
		else
		{
			KalturaLog::debug("File sync [" . $fileSync->getId() . "] path [$fullPath] does not exist");
		}
		
		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_PURGED);
		$fileSync->save();
	}
	
	/**
	 * @return array<FileSync>
	 */
	protected static function getDeletedFileSyncs()
	{
		$criteria = new Criteria();
		
		$linkCountCriterion = $criteria->getNewCriterion(FileSyncPeer::LINK_COUNT, 0);
		$linkCountCriterion->addOr($criteria->getNewCriterion(FileSyncPeer::LINK_COUNT, null, Criteria::ISNULL));
		
		$linkedIdCriterion = $criteria->getNewCriterion(FileSyncPeer::LINKED_ID, 0);
		$linkedIdCriterion->addOr($criteria->getNewCriterion(FileSyncPeer::LINKED_ID, null, Criteria::ISNULL));
		
		$criteria->add($linkCountCriterion);
		$criteria->add($linkedIdCriterion);
		$criteria->add(FileSyncPeer::DC, kDataCenterMgr::getCurrentDcId());
		$criteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_DELETED);
		$nextCriteria = clone $criteria;
		
		$criteria->add(FileSyncPeer::UPDATED_AT, self::$purgeStartUpdatedAt, Criteria::GREATER_EQUAL);
		$criteria->addAnd(FileSyncPeer::UPDATED_AT, self::$purgeEndUpdatedAt, Criteria::LESS_EQUAL);
		
		$criteria->addAscendingOrderByColumn(FileSyncPeer::UPDATED_AT);
		$criteria->setLimit(self::$queryLimit);
		
		$fileSyncs = FileSyncPeer::doSelect($criteria);
		if(count($fileSyncs))
		{
			$fileSync = end($fileSyncs);
			if($fileSync->getUpdatedAt(null))
				self::$purgeNextStartUpdatedAt = $fileSync->getUpdatedAt(null);
		}
		else
		{
			$nextCriteria->add(FileSyncPeer::UPDATED_AT, self::$purgeStartUpdatedAt, Criteria::GREATER_THAN);
			$nextCriteria->addSelectColumn('UNIX_TIMESTAMP(MIN(' . FileSyncPeer::UPDATED_AT . '))');
			$stmt = FileSyncPeer::doSelectStmt($nextCriteria);
			$mins = $stmt->fetchAll(PDO::FETCH_COLUMN);
			if(count($mins))
			{
				$purgeNextStartUpdatedAt = reset($mins);
				if(!is_null($purgeNextStartUpdatedAt))
					self::$purgeNextStartUpdatedAt = $purgeNextStartUpdatedAt;
			}
			
		}
		return $fileSyncs;
	}
	
	protected static function deleteDeletedPartnersFileSyncs()
	{
		$partnersCriteria = new Criteria();
		$partnersCriteria->add(PartnerPeer::STATUS, array(Partner::PARTNER_STATUS_DELETED, Partner::PARTNER_STATUS_FULL_BLOCK), Criteria::IN);
		$partnersCriteria->add(PartnerPeer::UPDATED_AT, self::$oldPartnersUpdatedAt, Criteria::LESS_THAN);
		$partnersCriteria->addAscendingOrderByColumn(PartnerPeer::UPDATED_AT);
		$partnersCriteria->setLimit(100);

		$offset = 0;
		$stmt = PartnerPeer::doSelectStmt($partnersCriteria);
		$partnerIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
		while(count($partnerIds))
		{
			$criteria = new Criteria();
			$criteria->add(FileSyncPeer::STATUS, array(FileSync::FILE_SYNC_STATUS_DELETED, FileSync::FILE_SYNC_STATUS_PENDING), Criteria::NOT_IN);
			$criteria->add(FileSyncPeer::PARTNER_ID, $partnerIds, Criteria::IN);
			$criteria->addDescendingOrderByColumn(FileSyncPeer::FILE_SIZE);
			$criteria->setLimit(self::$queryLimit);
		
			$fileSyncs = FileSyncPeer::doSelect($criteria);
			foreach($fileSyncs as $fileSync)
				self::deleteFileSync($fileSync);
			
			kMemoryManager::clearMemory();
			$offset += count($partnerIds);
			$partnersCriteria->setOffset($offset);
			$stmt = PartnerPeer::doSelectStmt($partnersCriteria);
			$partnerIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
		}
	}
	
	protected static function deleteErrorEntries()
	{
		$criteria = new Criteria();
		$criteria->add(entryPeer::STATUS, array(entryStatus::READY, entryStatus::DELETED), Criteria::NOT_IN);
		$criteria->add(entryPeer::UPDATED_AT, self::$errObjectsUpdatedAt, Criteria::LESS_THAN);
		$criteria->addSelectColumn('UNIX_TIMESTAMP(MIN(' . entryPeer::UPDATED_AT . '))');
		$stmt = entryPeer::doSelectStmt($criteria);
		$mins = $stmt->fetchAll(PDO::FETCH_COLUMN);
		if(!count($mins))
			return;
			
		$errObjectsUpdatedAtStart = reset($mins);
		if(is_null($errObjectsUpdatedAtStart))
			return;
			
		$errObjectsUpdatedAtEnd = min(self::$errObjectsUpdatedAt, $errObjectsUpdatedAtStart + (60 * 60 * 24 * 30)); // month
			
		$criteria = new Criteria();
		$criteria->add(entryPeer::STATUS, array(entryStatus::READY, entryStatus::DELETED), Criteria::NOT_IN);
		$criteria->add(entryPeer::UPDATED_AT, $errObjectsUpdatedAtStart, Criteria::GREATER_EQUAL);
		$criteria->addAnd(entryPeer::UPDATED_AT, $errObjectsUpdatedAtEnd, Criteria::LESS_THAN);
		$criteria->addDescendingOrderByColumn(entryPeer::LENGTH_IN_MSECS);
		$criteria->setLimit(self::$queryLimit);
	
		$entries = entryPeer::doSelect($criteria);
		foreach($entries as $entry)
		{
			/* @var $entry entry */
			KalturaLog::info("Deleting entry [" . $entry->getId() . "]");
			try
			{
				myEntryUtils::deleteEntry($entry);
			}
			catch (Exception $e)
			{
				KalturaLog::err($e);
			}
		}
			
		self::incrementSummary('entry', count($entries));
		kMemoryManager::clearMemory();
	}
	
	protected static function deleteErrorAssets()
	{
		$criteria = new Criteria();
		$criteria->add(assetPeer::STATUS, array(asset::ASSET_STATUS_READY, asset::ASSET_STATUS_DELETED), Criteria::NOT_IN);
		$criteria->add(assetPeer::UPDATED_AT, self::$errObjectsUpdatedAt, Criteria::LESS_THAN);
		$criteria->addSelectColumn('UNIX_TIMESTAMP(MIN(' . assetPeer::UPDATED_AT . '))');
		$stmt = assetPeer::doSelectStmt($criteria);
		$mins = $stmt->fetchAll(PDO::FETCH_COLUMN);
		if(!count($mins))
			return;
			
		$errObjectsUpdatedAtStart = reset($mins);
		if(is_null($errObjectsUpdatedAtStart))
			return;
			
		$errObjectsUpdatedAtEnd = min(self::$errObjectsUpdatedAt, $errObjectsUpdatedAtStart + (60 * 60 * 24 * 30)); // month
			
		$criteria = new Criteria();
		$criteria->add(assetPeer::STATUS, array(asset::ASSET_STATUS_READY, asset::ASSET_STATUS_DELETED), Criteria::NOT_IN);
		$criteria->add(assetPeer::UPDATED_AT, $errObjectsUpdatedAtStart, Criteria::LESS_THAN);
		$criteria->addAnd(entryPeer::UPDATED_AT, $errObjectsUpdatedAtEnd, Criteria::LESS_THAN);
		$criteria->addDescendingOrderByColumn(assetPeer::SIZE);
		$criteria->setLimit(self::$queryLimit);
	
		$assets = assetPeer::doSelect($criteria);
		foreach($assets as $asset)
		{
			/* @var $asset asset */
			KalturaLog::info("Deleting asset [" . $asset->getId() . "]");
			$asset->setStatus(asset::ASSET_STATUS_DELETED);
		
			try
			{
				$asset->save();
			}
			catch (Exception $e)
			{
				KalturaLog::err($e);
			}
		}
			
		self::incrementSummary('asset', count($assets));
		kMemoryManager::clearMemory();
	}
	
	protected static function deleteErrorObjects()
	{
		self::deleteErrorEntries();
		self::deleteErrorAssets();
	}
	
	protected static function deleteOldVersions()
	{
		foreach(self::$objectsToClean as $objectType => $objectSubTypes)
			foreach($objectSubTypes as $objectSubType)
				self::deleteOldVersionedFileSyncs($objectType, $objectSubType);
	}
	
	protected static function deleteOldVersionedFileSyncs($objectType, $objectSubType)
	{
		if(!isset(self::$oldVersionsStartUpdatedAt[$objectType]))
			self::$oldVersionsStartUpdatedAt[$objectType] = 0;
					
		if(!isset(self::$oldVersionsEndUpdatedAt[$objectType]))
			self::$oldVersionsEndUpdatedAt[$objectType] = 0;
			
		$criteria = new Criteria();
		
		switch ($objectType)
		{
			case FileSyncObjectType::ASSET:
				if($objectSubType != asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET)
					return array();
					
				$join = new Join();
		        $join->addCondition(FileSyncPeer::OBJECT_ID, assetPeer::ID);
		        $join->addCondition(FileSyncPeer::VERSION, assetPeer::VERSION, Criteria::NOT_EQUAL);
				$join->setJoinType(Criteria::LEFT_JOIN);
				$criteria->addJoinObject($join);
				$criteria->add(assetPeer::VERSION, null, Criteria::ISNOTNULL);
				break;
				
			case FileSyncObjectType::UICONF:
				$join = new Join();
		        $join->addCondition(FileSyncPeer::OBJECT_ID, uiConfPeer::ID);
		        $join->addCondition(FileSyncPeer::VERSION, uiConfPeer::VERSION, Criteria::NOT_EQUAL);
				$join->setJoinType(Criteria::LEFT_JOIN);
				$criteria->addJoinObject($join);
				$criteria->add(uiConfPeer::VERSION, null, Criteria::ISNOTNULL);
				break;
				
			case FileSyncObjectType::ENTRY:
				$join = new Join();
		        $join->addCondition(FileSyncPeer::OBJECT_ID, entryPeer::ID);
				        
				switch($objectSubType)
				{
					case kEntryFileSyncSubType::THUMB:
				        $join->addCondition(FileSyncPeer::VERSION, entryPeer::THUMBNAIL, Criteria::NOT_EQUAL);
						$criteria->add(entryPeer::THUMBNAIL, null, Criteria::ISNOTNULL);
				        break;
					
					case kEntryFileSyncSubType::DATA:
					case kEntryFileSyncSubType::DOWNLOAD:
				        $join->addCondition(FileSyncPeer::VERSION, entryPeer::DATA, Criteria::NOT_EQUAL);
						$criteria->add(entryPeer::DATA, null, Criteria::ISNOTNULL);
				        break;
					
					default:
						return array();
				}
				
				$join->setJoinType(Criteria::LEFT_JOIN);
				$criteria->addJoinObject($join);
				break;
				
			case FileSyncObjectType::METADATA:
				$join = new Join();
		        $join->addCondition(FileSyncPeer::OBJECT_ID, MetadataPeer::ID);
		        $join->addCondition(FileSyncPeer::VERSION, MetadataPeer::VERSION, Criteria::NOT_EQUAL);
				$join->setJoinType(Criteria::LEFT_JOIN);
				$criteria->addJoinObject($join);
				$criteria->add(MetadataPeer::VERSION, null, Criteria::ISNOTNULL);
				break;
				
			case FileSyncObjectType::METADATA_PROFILE:
				$join = new Join();
		        $join->addCondition(FileSyncPeer::OBJECT_ID, MetadataProfilePeer::ID);
		        
				switch($objectSubType)
				{
					case MetadataProfile::FILE_SYNC_METADATA_DEFINITION:
				        $join->addCondition(FileSyncPeer::VERSION, MetadataProfilePeer::FILE_SYNC_VERSION, Criteria::NOT_EQUAL);
						$criteria->add(MetadataProfilePeer::FILE_SYNC_VERSION, null, Criteria::ISNOTNULL);
				        break;
					
					case MetadataProfile::FILE_SYNC_METADATA_VIEWS:
				        $join->addCondition(FileSyncPeer::VERSION, MetadataProfilePeer::VIEWS_VERSION, Criteria::NOT_EQUAL);
						$criteria->add(MetadataProfilePeer::VIEWS_VERSION, null, Criteria::ISNOTNULL);
				        break;
					
					default:
						return array();
				}
				
				$join->setJoinType(Criteria::LEFT_JOIN);
				$criteria->addJoinObject($join);
				break;
				
			default:
				return array();
		}
		
		$criteria->add(FileSyncPeer::DC, kDataCenterMgr::getCurrentDcId());
		$criteria->add(FileSyncPeer::OBJECT_TYPE, $objectType);
		$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, $objectSubType);
		$criteria->add(FileSyncPeer::STATUS, array(FileSync::FILE_SYNC_STATUS_DELETED, FileSync::FILE_SYNC_STATUS_PURGED), Criteria::NOT_IN);
		$nextCriteria = clone $criteria;
		
		$criteria->add(FileSyncPeer::UPDATED_AT, self::$oldVersionsStartUpdatedAt[$objectType], Criteria::GREATER_EQUAL);
		$criteria->addAnd(FileSyncPeer::UPDATED_AT, self::$oldVersionsEndUpdatedAt[$objectType], Criteria::LESS_EQUAL);
		
		$criteria->addAscendingOrderByColumn(FileSyncPeer::UPDATED_AT);
		$criteria->setLimit(self::$queryLimit);
		
		$fileSyncs = FileSyncPeer::doSelect($criteria);
		if(count($fileSyncs))
		{
			foreach($fileSyncs as $fileSync)
			{
				/* @var $fileSync FileSync */
				self::deleteFileSync($fileSync);
				if($fileSync->getUpdatedAt(null))
					self::$oldVersionsNextStartUpdatedAt[$objectType] = $fileSync->getUpdatedAt(null);
			}
		}
		else 
		{
			self::$oldVersionsNextStartUpdatedAt[$objectType] = self::$oldVersionsStartUpdatedAt[$objectType];
			
			$nextCriteria->add(FileSyncPeer::UPDATED_AT, self::$oldVersionsStartUpdatedAt[$objectType], Criteria::GREATER_THAN);
			$nextCriteria->addSelectColumn('UNIX_TIMESTAMP(MIN(' . FileSyncPeer::UPDATED_AT . '))');
			$stmt = FileSyncPeer::doSelectStmt($nextCriteria);
			$mins = $stmt->fetchAll(PDO::FETCH_COLUMN);
			if(count($mins))
			{
				$oldVersionsNextStartUpdatedAt = reset($mins);
				if(!is_null($oldVersionsNextStartUpdatedAt))
					self::$oldVersionsNextStartUpdatedAt[$objectType] = $oldVersionsNextStartUpdatedAt;
			}
		}
		kMemoryManager::clearMemory();
	}
}

// -------------------------------------------

kOldContentCleaner::clean();
