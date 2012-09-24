<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class kYouTubeDistributionEventConsumer implements kBatchJobStatusEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		$jobTypes = array(
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE),
		);
		if(in_array($dbBatchJob->getJobType(), $jobTypes))
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		self::onDistributionJobUpdated($dbBatchJob, $dbBatchJob->getData());
			
		return true;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionJobUpdated(BatchJob $dbBatchJob, kDistributionJobData $data)
	{
		switch($dbBatchJob->getStatus())
		{
			case BatchJob::BATCHJOB_STATUS_ALMOST_DONE:
				return self::onDistributionJobUpdatedAlmostDone($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionJobUpdatedAlmostDone(BatchJob $dbBatchJob, kDistributionJobData $data)
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