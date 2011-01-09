<?php
class kMsnDistributionReportHandler implements kBatchJobStatusEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
			self::onDistributionFetchReportJobUpdated($dbBatchJob, $dbBatchJob->getData(), $twinJob);
			
		return true;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionFetchReportJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionFetchReportJobUpdated(BatchJob $dbBatchJob, kDistributionFetchReportJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionFetchReportJobFinished($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionFetchReportJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionFetchReportJobFinished(BatchJob $dbBatchJob, kDistributionFetchReportJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$providerData = $data->getProviderData();
		if($providerData instanceof kMsnDistributionJobProviderData)
		{
			$entryDistribution->putInCustomData('emailed', $providerData->getEmailed());
			$entryDistribution->putInCustomData('rated', $providerData->getRated());
			$entryDistribution->putInCustomData('blogged', $providerData->getBlogged());
			$entryDistribution->putInCustomData('reviewed', $providerData->getReviewed());
			$entryDistribution->putInCustomData('bookmarked', $providerData->getBookmarked());
			$entryDistribution->putInCustomData('playbackFailed', $providerData->getPlaybackFailed());
			$entryDistribution->putInCustomData('timeSpent', $providerData->getTimeSpent());
			$entryDistribution->putInCustomData('recommended', $providerData->getRecommended());
			
			$entryDistribution->save();
		}
		
		return $dbBatchJob;
	}
}