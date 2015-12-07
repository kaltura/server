<?php
/**
 * @package plugins.multiCenters
 * @subpackage lib
 */
class kMultiCentersManager
{
	
	/**
	 * @param string $entryId
	 * @param FileSync $object
	 * @param int $fileSyncId
	 * @param string $sourceFileUrl
	 * @return BatchJob
	 */
	public static function addFileSyncImportJob($entryId, FileSync $fileSync, $sourceFileUrl, BatchJob $parentJob = null, $fileSize = null)
	{
		$partnerId = $fileSync->getPartnerId();
		$fileSyncId = $fileSync->getId();
		$dc = $fileSync->getDc();
		
		KalturaLog::log(__METHOD__ . " entryId[$entryId], partnerId[$partnerId], fileSyncId[$fileSyncId], sourceFileUrl[$sourceFileUrl]");
		
		$fileSyncImportData = new kFileSyncImportJobData();
		$fileSyncImportData->setSourceUrl($sourceFileUrl);
		$fileSyncImportData->setFilesyncId($fileSyncId);
		$fileSyncImportData->setFileSize($fileSize);
		// tmpFilePath and destFilePath will be set later during get exlusive call on the target data center 
		
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(BatchJobType::FILESYNC_IMPORT, null, true, $dc);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setDc($dc);
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}

		$batchJob->setObjectId($fileSyncId);
		$batchJob->setObjectType(BatchJobObjectType::FILE_SYNC);
		
		//In case file sync is of type data and holds flavor asset than we need to check if its the source asset that is being synced and raise it sync priority 
		if($fileSync->getObjectType() == FileSyncObjectType::FLAVOR_ASSET && $fileSync->getObjectSubType() == entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA 
		 && $fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_FILE)
		{
			$assetdb = assetPeer::retrieveById($fileSync->getObjectId());
			if($assetdb)
			{
	    		$isSourceAsset = $assetdb->getIsOriginal();
	     
	    		if($isSourceAsset)
					$fileSyncImportData->setIsSourceAsset(true);
			}
		}
		
		KalturaLog::log("Creating Filesync Import job, with file sync id: $fileSyncId size: $fileSize"); 
		return kJobsManager::addJob($batchJob, $fileSyncImportData, BatchJobType::FILESYNC_IMPORT);
	}
	
}
