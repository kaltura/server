<?php
/**
 * Event consumer which finishes up the export process to Kontiki
 */
class kKontikiManager implements kBatchJobStatusEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob) {
		switch ($dbBatchJob->getStatus()) {
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				$data = $dbBatchJob->getData();
                /* @var $data kKontikiStorageExportJobData */
                $asset = assetPeer::retrieveById($data->getFlavorAssetId());
                $asset->addTags(KontikiPlugin::KONTIKI_ASSET_TAG);
                $asset->save();
                //Get Kontiki file sync and set the external URL
                $filesyncKey = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
                $kontikiFileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($filesyncKey);
				$kontikiFileSync->setFileRoot("");
                $kontikiFileSync->setFilePath($data->getContentMoid());      
                $kontikiFileSync->save();         
				break;
			case BatchJob::BATCHJOB_STATUS_FAILED:
                $entry = entryPeer::retrieveByPK($dbBatchJob->getEntryId());
                $entry->setStatus(entryStatus::ERROR_IMPORTING);
				$entry->save();
                break;
			default:
				
				break;
		}

		return true;
		
	}

	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob) {
		if ($dbBatchJob->getJobType() == BatchJobType::STORAGE_EXPORT
            && $dbBatchJob->getJobSubType() == KontikiPlugin::getStorageProfileProtocolCoreValue(KontikiStorageProfileProtocol::KONTIKI))
		{
		    return true;
		}
        
        return false;
	}


}