<?php
/**
 * @package plugins.captionSearch
 * @subpackage lib
 */
class kCaptionSearchFlowManager implements kObjectDataChangedEventConsumer, kObjectDeletedEventConsumer, kObjectAddedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if(class_exists('CaptionAsset') && $object instanceof CaptionAsset 
				&& CaptionSearchPlugin::isAllowedPartner($object->getPartnerId())
				&& $object->getStatus() == CaptionAsset::ASSET_STATUS_READY && $object->getLanguage() != CaptionAsset::MULTI_LANGUAGE){
						return true;
					}
					
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		return $this->indexEntry($object, $raisedJob);
	}

	/* (non-PHPdoc)
	 * @see kObjectDataChangedEventConsumer::shouldConsumeDataChangedEvent()
	 */
	public function shouldConsumeDataChangedEvent(BaseObject $object, $previousVersion = null)
	{
		if(class_exists('CaptionAsset') && $object instanceof CaptionAsset && $object->getLanguage() != CaptionAsset::MULTI_LANGUAGE)
			return CaptionSearchPlugin::isAllowedPartner($object->getPartnerId());
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDataChangedEventConsumer::objectDataChanged()
	 */
	public function objectDataChanged(BaseObject $object, $previousVersion = null, BatchJob $raisedJob = null)
	{
		return $this->indexEntry($object, $raisedJob);
	}
	
	private function indexEntry(BaseObject $object, BatchJob $raisedJob = null)
	{
		// updated in the entry in the indexing server
		$entry = $object->getentry();
		if($entry && $entry->getStatus() != entryStatus::DELETED)
		{
			$entry->setUpdatedAt(time());
			$entry->save();
			$entry->indexToSearchIndex();

			// When captions change on an entry, also reindex any entries that have relationships
			// with this entry (redirects, source/clips)
			$this->reindexRelatedEntries($entry->getId());
		}

		return true;
	}

	/**
	 * Find and reindex all entries related to the specified entry.
	 * This ensures that when captions change on an entry, any entries with a relationship
	 * will also get reindexed with the updated caption data.
	 * Relationships include:
	 * 1. Live entries redirecting to VOD entries
	 * 2. Clip entries derived from source entries
	 *
	 * @param string $entryId The entry ID to check for related entries
	 * @return void
	 */
	private function reindexRelatedEntries($entryId)
	{
		// Reindex live entries that redirect to this entry
		$this->reindexLiveEntriesRedirectingTo($entryId);

		// Reindex clip entries that use this entry as their source
		$this->reindexClipsOfSourceEntry($entryId);
	}

	/**
	 * Find and reindex all live entries that redirect to the specified entry
	 * This ensures that when captions change on a VOD entry, any live entries pointing to it
	 * will also get reindexed with the updated caption data
	 *
	 * @param string $entryId The entry ID to check for redirecting live entries
	 * @return void
	 */
	private function reindexLiveEntriesRedirectingTo($entryId)
	{
		// Create criteria to find live entries that redirect to this entry
		$c = new Criteria();
		$c->add(entryPeer::TYPE, array(entryType::LIVE_STREAM, entryType::LIVE_CHANNEL), Criteria::IN);
		$c->add(entryPeer::STATUS, entryStatus::DELETED, Criteria::NOT_EQUAL);

		// redirectEntryId is stored in custom_data
		$c->add(entryPeer::CUSTOM_DATA, '%"redirectEntryId"%', Criteria::LIKE);

		// Find entries that might have redirectEntryId
		$potentialEntries = entryPeer::doSelect($c);

		// Filter to only those that actually redirect to our target entry
		$redirectEntries = array();
		foreach ($potentialEntries as $potentialEntry) {
			if ($potentialEntry->getRedirectEntryId() == $entryId) {
				$redirectEntries[] = $potentialEntry;
			}
		}

		// Reindex each live entry that redirects to our VOD entry
		foreach ($redirectEntries as $liveEntry) {
			KalturaLog::info("Reindexing live entry {$liveEntry->getId()} that redirects to entry {$entryId}");
			$liveEntry->setUpdatedAt(time());
			$liveEntry->save();
			$liveEntry->indexToSearchIndex();
		}
	}

	/**
	 * Find and reindex all clip entries that use the specified entry as their source
	 * This ensures that when captions change on a source entry, any clips derived from it
	 * will also get reindexed with the updated caption data
	 *
	 * @param string $entryId The entry ID to check for clips
	 * @return void
	 */
	private function reindexClipsOfSourceEntry($entryId)
	{
		// Create criteria to find clip entries that use this entry as source
		$c = new Criteria();
		$c->add(entryPeer::TYPE, entryType::MEDIA_CLIP);
		$c->add(entryPeer::STATUS, entryStatus::DELETED, Criteria::NOT_EQUAL);

		// sourceEntryId and rootEntryId are stored in custom_data
		$c->add(entryPeer::CUSTOM_DATA, '%"sourceEntryId":"' . $entryId . '"%', Criteria::LIKE);

		// Find clips using this entry as source
		$clipEntries = entryPeer::doSelect($c);

		// Reindex each clip entry
		foreach ($clipEntries as $clipEntry) {
			// Check if the clip already has its own captions
			$clipCaptionAssets = assetPeer::retrieveByEntryId($clipEntry->getId(), array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)), array(asset::ASSET_STATUS_READY, asset::ASSET_STATUS_EXPORTING));

			// Only reindex clips that don't have their own captions (i.e. rely on source captions)
			if (!$clipCaptionAssets || !count($clipCaptionAssets)) {
				KalturaLog::info("Reindexing clip entry {$clipEntry->getId()} that uses source entry {$entryId}");
				$clipEntry->setUpdatedAt(time());
				$clipEntry->save();
				$clipEntry->indexToSearchIndex();
			}
		}
	}
	
	/**
	 * @param CaptionAsset $captionAsset
	 * @param BatchJob $parentJob
	 * @throws kCoreException FILE_NOT_FOUND
	 * @return BatchJob
	 */
	public function addParseCaptionAssetJob(CaptionAsset $captionAsset, BatchJob $parentJob = null)
	{
		$syncKey = $captionAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey, $isRemote);
		if(!$fileSync || $isRemote)
		{
			if(!PermissionPeer::isValidForPartner(CaptionPermissionName::IMPORT_REMOTE_CAPTION_FOR_INDEXING, $captionAsset->getPartnerId()))
				throw new kCoreException("File sync not found: $syncKey", kCoreException::FILE_NOT_FOUND);
			
			if (!$fileSync)
			{
				$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($syncKey);
				if (!$fileSync)
				{
					throw new kCoreException("File sync not found: $syncKey", kCoreException::FILE_NOT_FOUND);
				}
			}
			
	    	$fullPath = myContentStorage::getFSUploadsPath() . '/' . $captionAsset->getId() . '.tmp';
			if(!KCurlWrapper::getDataFromFile($fileSync->getExternalUrl($captionAsset->getEntryId()), $fullPath, null, true))
				throw new kCoreException("File sync not found: $syncKey", kCoreException::FILE_NOT_FOUND);
			
			kFileSyncUtils::moveFromFile($fullPath, $syncKey, true, false, true);
		}
		
		$jobData = new kParseCaptionAssetJobData();
		$jobData->setCaptionAssetId($captionAsset->getId());
			
 		$batchJobType = CaptionSearchPlugin::getBatchJobTypeCoreValue(CaptionSearchBatchJobType::PARSE_CAPTION_ASSET);
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild($batchJobType);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($captionAsset->getEntryId());
			$batchJob->setPartnerId($captionAsset->getPartnerId());
		}
			
		$batchJob->setObjectId($captionAsset->getId());
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		return kJobsManager::addJob($batchJob, $jobData, $batchJobType);
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		/* @var $object CaptionAsset */
		// updates entry on order to trigger reindexing
		$this->indexEntry($object);
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof CaptionAsset && $object->getLanguage() != CaptionAsset::MULTI_LANGUAGE)
			return true;
			
		return false;
	}
}
