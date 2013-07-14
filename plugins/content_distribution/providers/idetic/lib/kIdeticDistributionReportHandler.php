<?php
/**
 * @package plugins.ideticDistribution
 * @subpackage lib
 */
class kIdeticDistributionReportHandler implements kBatchJobStatusEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_FETCH_REPORT))
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		self::onDistributionFetchReportJobUpdated($dbBatchJob, $dbBatchJob->getData());
			
		return true;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionFetchReportJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionFetchReportJobUpdated(BatchJob $dbBatchJob, kDistributionFetchReportJobData $data)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionFetchReportJobFinished($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionFetchReportJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionFetchReportJobFinished(BatchJob $dbBatchJob, kDistributionFetchReportJobData $data)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$providerData = $data->getProviderData();
/*		if($providerData instanceof kIdeticDistributionJobProviderData)
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
	*/	
		return $dbBatchJob;
	}
}