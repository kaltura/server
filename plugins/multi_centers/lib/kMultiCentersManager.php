<?php

class kMultiCentersManager
{
	
	/**
	 * @param string $entryId
	 * @param int $partnerId
	 * @param int $fileSyncId
	 * @param string $sourceFileUrl
	 * @return BatchJob
	 */
	public static function addFileSyncImportJob($entryId, $partnerId, $fileSyncId, $sourceFileUrl, BatchJob $parnetJob = null)
	{
		KalturaLog::log(__METHOD__ . " entryId[$entryId], partnerId[$partnerId], fileSyncId[$fileSyncId], sourceFileUrl[$sourceFileUrl]");
		
		$fileSyncImportData = new kFileSyncImportJobData();
		$fileSyncImportData->setSourceUrl($sourceFileUrl);
		$fileSyncImportData->setFilesyncId($fileSyncId);
		// tmpFilePath and destFilePath will be set later during get exlusive call on the target data center 
		
		$batchJob = null;
		if($parnetJob)
		{
			$batchJob = $parentJob->createChild();
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}
		
		KalturaLog::log("Creating Filesync Import job, with file sync id: $fileSyncId"); 
		return kJobsManager::addJob($batchJob, $fileSyncImportData, BatchJobType::FILESYNC_IMPORT);
	}
	
}