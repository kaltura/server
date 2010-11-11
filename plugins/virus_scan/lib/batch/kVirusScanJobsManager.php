<?php
class kVirusScanJobsManager extends kJobsManager
{
	/**
	 * @param BatchJob $parentJob
	 * @param int $partnerId
	 * @param string $entryId
	 * @param string $flavorAssetId
	 * @param string $srcFilePath
	 * @param KalturaVirusScanEngineType $virusScanEngine
	 * @return BatchJob
	 */
	public static function addVirusScanJob(BatchJob $parentJob = null, $partnerId, $entryId, $flavorAssetId, $srcFilePath, $virusScanEngine)
	{
 		$jobData = new kVirusScanJobData();
 		$jobData->setSrcFilePath($srcFilePath);
 		$jobData->setFlavorAssetId($flavorAssetId);
 		
		$batchJob = null;
		if($parentJob)
		{
			$batchJob = $parentJob->createChild();
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}
		return self::addJob($batchJob, $jobData, BatchJob::BATCHJOB_TYPE_VIRUS_SCAN, $virusScanEngine);
	}
}