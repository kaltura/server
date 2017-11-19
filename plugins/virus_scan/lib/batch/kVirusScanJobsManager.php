<?php
class kVirusScanJobsManager extends kJobsManager
{
	/**
	 * @param BatchJob $parentJob
	 * @param int $partnerId
	 * @param string $entryId
	 * @param string $flavorAssetId
	 * @param FileSyncKey $syncKey
	 * @param VirusScanEngineType $virusScanEngine
	 * @param int $scanProfileId
	 * @return BatchJob
	 */
	public static function addVirusScanJob(BatchJob $parentJob = null, $partnerId, $entryId, $flavorAssetId, $syncKey, $virusScanEngine, $virusFoundAction)
	{
		$jobType = VirusScanPlugin::getBatchJobTypeCoreValue(VirusScanBatchJobType::VIRUS_SCAN);
		
 		$jobData = new kVirusScanJobData();
 		$jobData->setFileData(self::getFileData($syncKey));
 		$jobData->setFlavorAssetId($flavorAssetId);
 		$jobData->setVirusFoundAction($virusFoundAction);

		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild($jobType, $virusScanEngine);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}
		
		$batchJob->setObjectId($flavorAssetId);
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		
		return self::addJob($batchJob, $jobData, $jobType, $virusScanEngine);
	}

	private static function getFileData(FileSyncKey $syncKey)
	{
		$fileData = new KalturaFile();
		$file_sync = kFileSyncUtils::getLocalFileSyncForKey( $syncKey , false );
		$fileData->filePath = $file_sync->getFullPath();
		$fileData->encryptionKey = $file_sync->getEncryptionKey();
		return $fileData;
	}
}