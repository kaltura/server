<?php
class kVirusScanFlowManager implements kBatchJobStatusEventConsumer, kObjectAddedEventConsumer
{
	
	/**
	 * @param BaseObject $object
	 * @return bool true if should continue to the next consumer
	 */
	public function objectAdded(BaseObject $object)
	{
		
		if($object instanceof flavorAsset && $object->getIsOriginal())
		{			
			$profile = VirusScanProfilePeer::getSuitableProfile($object->getEntryId());
			
			if ($profile)
			{
				// suitable virus scan profile found - create scan job
				$syncKey = $object->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				$srcFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
				kVirusScanJobsManager::addVirusScanJob(null, $object->getPartnerId(), $object->getEntryId(), $object->getId(), $srcFilePath, $profile->getEngineType(), $profile->getActionIfInfected());
				return false; // pause other event consumers until virus scan job is finished
			}
		}
		
		return true; // no scan jobs to do, object added event consumption may continue normally
	}
	
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param unknown_type $entryStatus
	 * @param BatchJob $twinJob
	 * @return bool true if should continue to the next consumer
	 */
	public function updatedJob(BatchJob $dbBatchJob, $entryStatus, BatchJob $twinJob = null)
	{
		if($dbBatchJob->getJobType() == VirusScanBatchJobType::get()->coreValue(VirusScanBatchJobType::VIRUS_SCAN))
			$dbBatchJob = $this->updatedVirusScan($dbBatchJob, $dbBatchJob->getData(), $entryStatus, $twinJob);
		
		return true;
	}
		
	protected function updatedVirusScan(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_PENDING:
				return $this->updatedVirusScanPending($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_QUEUED:
				return $this->updatedVirusScanQueued($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSING:
				return $this->updatedVirusScanProcessing($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_PROCESSED:
				return $this->updatedVirusScanProcessed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_MOVEFILE:
				return $this->updatedVirusScanMoveFile($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedVirusScanFinished($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
				return $this->updatedVirusScanFailed($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ABORTED:
				return $this->updatedVirusScanAborted($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return $this->updatedVirusScanAlmostDone($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_RETRY:
				return $this->updatedVirusScanRetry($dbBatchJob, $data, $entryStatus, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedVirusScanFatal($dbBatchJob, $data, $entryStatus, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedVirusScanPending(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanQueued(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanProcessing(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanProcessed(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanMoveFile(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanFinished(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		$flavorAsset = flavorAssetPeer::retrieveById($data->getFlavorAssetId());
		if (!$flavorAsset)
		{
			KalturaLog::err('Flavor asset not found with id ['.$data->getFlavorAssetId().']');
			throw new Exception('Flavor asset not found with id ['.$data->getFlavorAssetId().']');
		}
				
		switch ($data->getScanResult())
		{
			case KalturaVirusScanJobResult::FILE_WAS_CLEANED:
				// delete old filsync + create new + assign new version to flavor asset
				$oldSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				$oldFilePath = kFileSyncUtils::getLocalFilePathForKey($oldSyncKey);
				$flavorAsset->incrementVersion();
				$flavorAsset->save();				
				$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);	
				kFileSyncUtils::moveFromFile($oldFilePath, $syncKey);
				// resume flavor asset added event consumption
				kEventsManager::continueEvent(new kObjectAddedEvent($flavorAsset), 'kVirusScanFlowManager');
				break;
											
			case KalturaVirusScanJobResult::FILE_IS_CLEAN:
				// resume flavor asset added event consumption
				kEventsManager::continueEvent(new kObjectAddedEvent($flavorAsset), 'kVirusScanFlowManager');
				break;
				
			case KalturaVirusScanJobResult::FILE_INFECTED:
				$entry = $flavorAsset->getentry();
				if (!$entry) {
					KalturaLog::err('Entry not found with id ['.$entry->getId().']');
				}
				else {
					$entry->setStatus(VirusScanEntryStatus::get()->apiValue(VirusScanEntryStatus::INFECTED));
					$entry->save();
				}
				
				// delete flavor asset and entry if defined in virus scan profile
				$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);		
				
				if ( $data->getVirusFoundAction() == KalturaVirusFoundAction::CLEAN_DELETE ||
					 $data->getVirusFoundAction() == KalturaVirusFoundAction::DELETE          )
				{
					$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
					$filePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
					KalturaLog::debug('FlavorAsset ['.$flavorAsset->getId().'] marked as deleted');
					$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
					$flavorAsset->setDeletedAt(time());					
					KalturaLog::debug('Physically deleting file ['.$filePath.']');
					unlink($filePath);
					if ($entry)	{
						myEntryUtils::deleteEntry($entryToDelete);
					}
				}
				$flavorAsset->save();
				
				myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $entry);
				// do not resume flavor asset added event consumption
				break;
		}		
		
		return $dbBatchJob;
	}
	
	protected function updatedVirusScanFailed(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		$entry = entryPeer::retrieveByPK($dbBatchJob->getEntryId());
		if ($entry)
		{
			$entry->setStatus(VirusScanEntryStatus::get()->coreValue(VirusScanEntryStatus::INFECTED));
			$entry->save();
		}
		else
		{
			KalturaLog::err('Entry not found with id ['.$dbBatchJob->getEntryId().']');
			throw new Exception('Entry not found with id ['.$dbBatchJob->getEntryId().']');
		}
		$flavorAsset = flavorAssetPeer::retrieveById($data->getFlavorAssetId());
		if ($flavorAsset)
		{
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
			$flavorAsset->save();
		}
		else
		{
			KalturaLog::err('Flavor asset not found with id ['.$data->getFlavorAssetId().']');
			throw new Exception('Flavor asset not found with id ['.$data->getFlavorAssetId().']');
		}					
		// do not resume flavor asset added event consumption
		return $dbBatchJob;
	}
	
	protected function updatedVirusScanAborted(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanAlmostDone(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanRetry(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null){ return $dbBatchJob; }
	
	protected function updatedVirusScanFatal(BatchJob $dbBatchJob, kVirusScanJobData $data, $entryStatus, BatchJob $twinJob = null)
	{
		return $this->updatedVirusScanFailed($dbBatchJob, $data, $entryStatus, $twinJob);
	}
}