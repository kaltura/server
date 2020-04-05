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
			$deletedAsset->save();
		}

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

	protected function restoreFileSyncs( array $fileSyncs )
	{
		foreach ($fileSyncs as $fileSync)
		{
			$shouldUnDelete = false;
			if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_FILE
				|| $fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
			{
				$shouldUnDelete = true;
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

		foreach ($fileSyncs as $fileSync) {
			if ($fileSync->getStatus() != FileSync::FILE_SYNC_STATUS_DELETED) {
				return false;
			}
		}

		foreach ($assets as $asset) {
			if ($asset->getStatus() != asset::ASSET_STATUS_DELETED) {
				return false;
			}
		}

		return true;
	}
}
