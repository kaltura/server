<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class kYouTubeDistributionEventConsumer implements kBatchJobStatusEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		$jobTypes = array(
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE),
		);
		if(in_array($dbBatchJob->getJobType(), $jobTypes))
		{
			self::onDistributionJobUpdated($dbBatchJob, $dbBatchJob->getData(), $twinJob);
		}
			
		return true;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionJobUpdated(BatchJob $dbBatchJob, kDistributionJobData $data, BatchJob $twinJob = null)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return self::onDistributionJobUpdatedAlmostDone($dbBatchJob, $data, $twinJob);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionJobData $data
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public static function onDistributionJobUpdatedAlmostDone(BatchJob $dbBatchJob, kDistributionJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$providerData = $data->getProviderData();
		KalturaLog::crit('provider data type' . get_class($providerData));
		if($providerData instanceof kYouTubeDistributionJobProviderData)
		{
			KalturaLog::debug('setting currentPlaylists to entryDistribution custom data');
			$entryDistribution->putInCustomData('currentPlaylists', $providerData->getCurrentPlaylists());
			$entryDistribution->save();
		}
		
		return $dbBatchJob;
	}
}