<?php
/**
 * Entry Admin service
 *
 * @service entryAdmin
 * @package plugins.adminConsole
 * @subpackage api.services
 */
class EntryAdminService extends KalturaBaseService
{
	const GET_TRACKS_LIMIT = 30;
	const PURGED_SUFFIX = "_purged";

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if(!AdminConsolePlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, AdminConsolePlugin::PLUGIN_NAME);
	}

	/**
	 * Get base entry by ID with no filters.
	 * 
	 * @action get
	 * @param string $entryId Entry id
	 * @param int $version Desired version of the data
	 * @return KalturaBaseEntry The requested entry
	 */
	function getAction($entryId, $version = -1)
	{
		$dbEntries = entryPeer::retrieveByPKsNoFilter(array($entryId));
		if (!count($dbEntries))
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		
		$dbEntry = reset($dbEntries);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ($version !== -1)
			$dbEntry->setDesiredVersion($version);
			
	    $entry = KalturaEntryFactory::getInstanceByType($dbEntry->getType(), true);
	    
		$entry->fromObject($dbEntry, $this->getResponseProfile());

		return $entry;
	}
	
	/**
	 * Get base entry by flavor ID with no filters.
	 * 
	 * @action getByFlavorId
	 * @param string $flavorId
	 * @param int $version Desired version of the data
	 * @return KalturaBaseEntry The requested entry
	 */
	public function getByFlavorIdAction($flavorId, $version = -1)
	{
		$flavorAssetDb = assetPeer::retrieveById($flavorId);
		if (!$flavorAssetDb)
			throw new KalturaAPIException(KalturaErrors::FLAVOR_ASSET_ID_NOT_FOUND, $flavorId);
			
		return $this->getAction($flavorAssetDb->getEntryId(), $version);
	}

	/**
	 * Get base entry by ID with no filters.
	 * 
	 * @action getTracks
	 * @param string $entryId Entry id
	 * @return KalturaTrackEntryListResponse
	 */
	function getTracksAction($entryId)
	{
		$c = new Criteria();
		$c->add(TrackEntryPeer::ENTRY_ID, $entryId);
		$c->setLimit(self::GET_TRACKS_LIMIT);
		$c->addAscendingOrderByColumn(TrackEntryPeer::CREATED_AT);
		
		$dbList = TrackEntryPeer::doSelect($c);
		
		$list = KalturaTrackEntryArray::fromDbArray($dbList, $this->getResponseProfile());
		$response = new KalturaTrackEntryListResponse();
		$response->objects = $list;
		$response->totalCount = count($dbList);
		return $response;
	}

	/**
	 * Restore deleted entry.
	 *
	 * @action restoreDeletedEntry
	 * @param string $entryId
	 * @return KalturaBaseEntry The restored entry
	 */
	public function restoreDeletedEntryAction($entryId)
	{
		$deletedEntry = entryPeer::retrieveByPKNoFilter($entryId);
		if (!$deletedEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$fileSyncKeys = array();
		foreach (entry::getEntryFileSyncSubTypes() as $entrySubType) {
			$fileSyncKeys[] = $deletedEntry->getSyncKey($entrySubType);
		}

		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId, Criteria::EQUAL);
		assetPeer::setUseCriteriaFilter(false);
		$deletedAssets = assetPeer::doSelect($c);
		assetPeer::setUseCriteriaFilter(true);

		foreach($deletedAssets as $deletedAsset)
		{
			array_push($fileSyncKeys, $deletedAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET), $deletedAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_CONVERT_LOG));
		}

		$fileSyncs = array();
		FileSyncPeer::setUseCriteriaFilter(false);
		foreach ($fileSyncKeys as $fileSyncKey)
		{
			$fileSyncs = array_merge($fileSyncs, FileSyncPeer::retrieveAllByFileSyncKey($fileSyncKey));
		}
		FileSyncPeer::setUseCriteriaFilter(true);

		if (!$this->validateEntryForRestoreDelete($deletedEntry, $fileSyncs, $deletedAssets))
			throw new KalturaAPIException(KalturaAdminConsoleErrors::ENTRY_ASSETS_WRONG_STATUS_FOR_RESTORE, $entryId);

		$this->restoreFileSyncs($fileSyncs);

		//restore assets
		foreach($deletedAssets as $deletedAsset)
		{
			$deletedAsset->setStatus(asset::ASSET_STATUS_READY);
			$deletedAsset->setDeletedAt(null);
			$deletedAsset->save();
		}

		$this->restoreCategoryEntries($deletedEntry);
		$this->restoreMetadata($deletedEntry);
		//restore entry
		$deletedEntry->setStatusReady();
		$deletedEntry->setThumbnail($deletedEntry->getFromCustomData("deleted_original_thumb"), true);
		$deletedEntry->setData($deletedEntry->getFromCustomData("deleted_original_data"),true); //data should be resotred even if it's NULL
		$deletedEntry->save();

		kEventsManager::flushEvents();
		kMemoryManager::clearMemory();

		$entry = KalturaEntryFactory::getInstanceByType($deletedEntry->getType(), true);
		$entry->fromObject($deletedEntry, $this->getResponseProfile());
		return $entry;
	}

	protected function restoreCategoryEntries(entry $deletedEntry)
	{
		KalturaLog::debug("restoreCategoryEntries");
		$c = new Criteria();
		$c->add(categoryEntryPeer::ENTRY_ID, $deletedEntry->getId(), Criteria::EQUAL);
		$c->add(categoryEntryPeer::STATUS, CategoryEntryStatus::DELETED, Criteria::EQUAL);
		$c->add(categoryEntryPeer::UPDATED_AT, $deletedEntry->getUpdatedAt(), Criteria::GREATER_EQUAL);
		categoryEntryPeer::setUseCriteriaFilter(false);
		$deletedCategoryEntries = categoryEntryPeer::doSelect($c);
		categoryEntryPeer::setUseCriteriaFilter(true);
		KalturaLog::debug("Category entries: ". print_r($deletedCategoryEntries, true));
		foreach($deletedCategoryEntries as $categoryEntry)
		{
			KalturaLog::debug("Going to restore categoryEntry [{$categoryEntry->getId()}]");
			$categoryEntry->setStatus(CategoryEntryStatus::ACTIVE);
			$categoryEntry->save();
		}
	}

	/*
	 * Restore metadata based on this query:
	 * Per metadata_profile_id with status deleted get only the objects with the higher id (the newest ones)
	 *
	 * Step1: SELECT metadata_profile_id, MAX(id) AS max_id FROM `metadata` WHERE metadata.STATUS=3 and object_id=x GROUP BY metadata_profile_id;
	 * results will look like: [['metadata_profile_id' => 101, 'max_id' => y],['metadata_profile_id' => 102, 'max_id' => z]]
	 * Step2: SELECT * FROM `metadata` WHERE metadata.ID IN ('y,z');
	 */
	protected function restoreMetadata(entry $deletedEntry)
	{
		KalturaLog::debug("restoreMetadata");

		// Step 1: Create criteria for fetching the maximum `id` per `metadata_profile_id` with conditions
		$cMax = new Criteria();
		$cMax->clearSelectColumns();
		$cMax->addSelectColumn('metadata_profile_id');
		$cMax->addSelectColumn('MAX(id) AS max_id');
		$cMax->addGroupByColumn('metadata_profile_id');
		$cMax->add(MetadataPeer::STATUS, KalturaMetadataStatus::DELETED, Criteria::EQUAL);
		$cMax->add(MetadataPeer::OBJECT_ID, $deletedEntry->getId(), Criteria::EQUAL);
		MetadataPeer::setUseCriteriaFilter(false);
		//return the result as associative array
		$maxIds = MetadataPeer::doSelectStmt($cMax)->fetchAll(PDO::FETCH_ASSOC);

		// Step 2: Use the max IDs to fetch the full metadata rows
		$c = new Criteria();
		$ids = array_column($maxIds, 'max_id');
		$c->add(MetadataPeer::ID, $ids, Criteria::IN);
		$metadataEntries = MetadataPeer::doSelect($c);
		MetadataPeer::setUseCriteriaFilter(true);
		foreach($metadataEntries as $metadataEntry)
		{
			KalturaLog::debug("Going to restore metadata id: [{$metadataEntry->getId()}]");
			$metadataEntry->setStatus(KalturaMetadataStatus::VALID);
			$metadataEntry->save();
		}

	}

	protected function restoreFileSyncs( array $fileSyncs )
	{
		foreach ($fileSyncs as $fileSync)
		{
			$shouldUnDelete = false;
			if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_FILE
				|| $fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
			{
				if (kFile::checkFileExists($fileSync->getFullPath()))
				{
					$shouldUnDelete = true;
				}
			}
			else if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_LINK){
				$linkedId = $fileSync->getLinkedId();
				FileSyncPeer::setUseCriteriaFilter(false);
				$linkedFileSync = FileSyncPeer::retrieveByPK($linkedId);
				FileSyncPeer::setUseCriteriaFilter(true);
				if ($linkedFileSync->getStatus() == FileSync::FILE_SYNC_STATUS_READY) {
					$shouldUnDelete = true;
					kFileSyncUtils::incrementLinkCountForFileSync($linkedFileSync);
				}
			}

			if ($shouldUnDelete)
				$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
			else
				$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_ERROR);
			$fileSync->save();
		}
	}

	protected function validateEntryForRestoreDelete(entry $entry, array $fileSyncs, array $assets)
	{
		if ( $entry->getStatus()!= entryStatus::DELETED )
			return false;

		$atLeastOneDeletedFileSync = false;
		foreach ($fileSyncs as $fileSync) {
			if (!in_array($fileSync->getStatus(),array(FileSync::FILE_SYNC_STATUS_DELETED, FileSync::FILE_SYNC_STATUS_PURGED))) {
				return false;
			}
			if ($fileSync->getStatus() == FileSync::FILE_SYNC_STATUS_DELETED && !str_ends_with($fileSync->getFilePath(), self::PURGED_SUFFIX))
			{
				$atLeastOneDeletedFileSync = true;
			}
		}

		if ($fileSyncs && !$atLeastOneDeletedFileSync) {
			return false;
		}

		foreach ($assets as $asset) {
			if ($asset->getStatus() != asset::ASSET_STATUS_DELETED) {
				return false;
			}
		}

		return true;
	}
}
