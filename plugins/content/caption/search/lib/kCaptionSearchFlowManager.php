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
		}

		return true;
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
