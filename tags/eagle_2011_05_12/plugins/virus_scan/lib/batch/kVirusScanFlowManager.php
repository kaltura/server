<?php
class kVirusScanFlowManager implements kBatchJobStatusEventConsumer, kObjectAddedEventConsumer
{
	
	private static $flavorAssetIdsToScan = array();
	
	
	private function resumeEvents($flavorAsset, BatchJob $raisedJob = null)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		
		$c = FileSyncPeer::getCriteriaForFileSyncKey( $syncKey );
		$fileSyncList = FileSyncPeer::doSelect( $c );
				
		foreach ($fileSyncList as $fileSync)
		{
			// resume file sync added event
			kEventsManager::continueEvent(new kObjectAddedEvent($fileSync, $raisedJob), 'kVirusScanFlowManager');
		}

		// resume flavor asset added event consumption
		kEventsManager::continueEvent(new kObjectAddedEvent($flavorAsset, $raisedJob), 'kVirusScanFlowManager');
	}
	
	
	private function saveIfShouldScan($flavorAsset)
	{
		if (!PermissionPeer::isAllowedPlugin(VirusScanPlugin::PLUGIN_NAME, $flavorAsset->getPartnerId()))
			return false;
		
		if (isset(self::$flavorAssetIdsToScan[$flavorAsset->getId()]))
		{
			return true;
		}
		
		$profile = VirusScanProfilePeer::getSuitableProfile($flavorAsset->getEntryId());
		if ($profile)
		{
			self::$flavorAssetIdsToScan[$flavorAsset->getId()] = $profile;
			return true;
		}
		
		return false;
	}
	
	/**
	 * @param FileSync $object
	 * @return bool true if should continue to the next consumer
	 */
	private function addedFileSync(FileSync $object)
	{
		if(!($object instanceof FileSync) || $object->getStatus() != FileSync::FILE_SYNC_STATUS_PENDING || $object->getFileType() != FileSync::FILE_SYNC_FILE_TYPE_FILE)
			return true;
			
		if ($object->getObjectType() != FileSyncObjectType::FLAVOR_ASSET)
			return true;
		
		$flavorAssetId = $object->getObjectId();
		$flavorAsset = flavorAssetPeer::retrieveById($flavorAssetId);
		if (!$flavorAsset || !$flavorAsset->getIsOriginal())
			return true;

		if ($this->saveIfShouldScan($flavorAsset))
		{
			// file sync belongs to a flavor asset in status pending and suits a virus scan profile
			return false; // stop all remaining consumers
		}			
		
		return true;
	}
	
	/**
	 * @param flavorAsset $object
	 * @return bool true if should continue to the next consumer
	 */
	private function addedFlavorAsset(flavorAsset $object, BatchJob $raisedJob = null)
	{
		if($object instanceof flavorAsset && $object->getIsOriginal())
		{
			if ($this->saveIfShouldScan($object))
			{
				$profile = self::$flavorAssetIdsToScan[$object->getId()];
				
				// suitable virus scan profile found - create scan job
				$syncKey = $object->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				$srcFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
				kVirusScanJobsManager::addVirusScanJob($raisedJob, $object->getPartnerId(), $object->getEntryId(), $object->getId(), $srcFilePath, $profile->getEngineType(), $profile->getActionIfInfected());
				return false; // pause other event consumers until virus scan job is finished
			}
		}
		
		return true; // no scan jobs to do, object added event consumption may continue normally
	}
	

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		// virus scan only works in api_v3 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || !kCurrentContext::isApiV3Context()) {
			return true;
		}
		
		$response = true;
		if($object instanceof flavorAsset)
		{
			$response = $this->addedFlavorAsset($object, $raisedJob);
		}
		
		if($object instanceof FileSync)
		{
			$response = $this->addedFileSync($object);
		}
		
		if (!$response) {
			KalturaLog::debug('kVirusScanFlowManager stopping consumption of event ['.get_class($object).']');
		}
		return $response;	
	}
	
	
	/**
	 * @param BatchJob $dbBatchJob
	 * @param BatchJob $twinJob
	 * @return bool true if should continue to the next consumer
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		// virus scan only works in api_v3 context because it uses dynamic enums
		if (!class_exists('kCurrentContext') || !kCurrentContext::isApiV3Context()) {
			return true;
		}
		
		if($dbBatchJob->getJobType() == VirusScanPlugin::getBatchJobTypeCoreValue(VirusScanBatchJobType::VIRUS_SCAN))
			$dbBatchJob = $this->updatedVirusScan($dbBatchJob, $dbBatchJob->getData(), $twinJob);

		return true;
	}
		
	protected function updatedVirusScan(BatchJob $dbBatchJob, kVirusScanJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return $this->updatedVirusScanFinished($dbBatchJob, $data, $twinJob);
			case BatchJob::BATCHJOB_STATUS_FAILED:
			case BatchJob::BATCHJOB_STATUS_FATAL:
				return $this->updatedVirusScanFailed($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}
	
	protected function updatedVirusScanFinished(BatchJob $dbBatchJob, kVirusScanJobData $data, BatchJob $twinJob = null)
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
			case KalturaVirusScanJobResult::FILE_IS_CLEAN:
				$this->resumeEvents($flavorAsset, $dbBatchJob);
				break;
				
			case KalturaVirusScanJobResult::FILE_INFECTED:
				$entry = $flavorAsset->getentry();
				if (!$entry) {
					KalturaLog::err('Entry not found with id ['.$entry->getId().']');
				}
				else {
					$entry->setStatus(VirusScanPlugin::getEntryStatusCoreValue(VirusScanEntryStatus::INFECTED));
					$entry->save();
				}
				
				// delete flavor asset and entry if defined in virus scan profile	
				if ( $data->getVirusFoundAction() == KalturaVirusFoundAction::CLEAN_DELETE ||
					 $data->getVirusFoundAction() == KalturaVirusFoundAction::DELETE          )
				{
					$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
					$filePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
					KalturaLog::debug('FlavorAsset ['.$flavorAsset->getId().'] marked as deleted');
					$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
					$flavorAsset->setDeletedAt(time());
					$flavorAsset->save();					
					KalturaLog::debug('Physically deleting file ['.$filePath.']');
					unlink($filePath);
					if ($entry)	{
						myEntryUtils::deleteEntry($entry);
					}
				}
				else {
					$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_ERROR);
					$flavorAsset->save();
				}				
				
				myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $entry);
				// do not resume flavor asset added event consumption
				break;
		}		
		
		return $dbBatchJob;
	}
	
	protected function updatedVirusScanFailed(BatchJob $dbBatchJob, kVirusScanJobData $data, BatchJob $twinJob = null)
	{
		$entry = entryPeer::retrieveByPK($dbBatchJob->getEntryId());
		if ($entry)
		{
			$entry->setStatus(VirusScanPlugin::getEntryStatusCoreValue(VirusScanEntryStatus::INFECTED));
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
}