<?php

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
		// tmpFilePath and destFilePath will be set later during get exlusive call on the target data center 
		
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild(true, $dc);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setDc($dc);
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}

		$batchJob->setFileSize($fileSize);
		
		KalturaLog::log("Creating Filesync Import job, with file sync id: $fileSyncId size: $fileSize"); 
		return kJobsManager::addJob($batchJob, $fileSyncImportData, BatchJobType::FILESYNC_IMPORT);
	}
	
}
