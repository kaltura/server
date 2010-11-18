<?php
class kVirusScanJobsManager extends kJobsManager
{
	/**
	 * @param BatchJob $parentJob
	 * @param int $partnerId
	 * @param string $entryId
	 * @param string $flavorAssetId
	 * @param string $srcFilePath
	 * @param VirusScanEngineType $virusScanEngine
	 * @param int $scanProfileId
	 * @return BatchJob
	 */
	public static function addVirusScanJob(BatchJob $parentJob = null, $partnerId, $entryId, $flavorAssetId, $srcFilePath, $virusScanEngine, $virusFoundAction)
	{
 		$jobData = new kVirusScanJobData();
 		$jobData->setSrcFilePath($srcFilePath);
 		$jobData->setFlavorAssetId($flavorAssetId);
 		$jobData->setVirusFoundAction($virusFoundAction);

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
		
		$jobType = VirusScanBatchJobType::get()->coreValue(VirusScanBatchJobType::VIRUS_SCAN);
		return self::addJob($batchJob, $jobData, $jobType, $virusScanEngine);
	}
}