<?php
/**
 * @package plugins.uverseDistribution
 * @subpackage lib
 */
class kUverseDistributionEventConsumer implements kBatchJobStatusEventConsumer
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
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{
		self::onDistributionJobUpdated($dbBatchJob, $dbBatchJob->getData(), $twinJob);
			
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
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionJobFinished($dbBatchJob, $data, $twinJob);
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
	public static function onDistributionJobFinished(BatchJob $dbBatchJob, kDistributionJobData $data, BatchJob $twinJob = null)
	{
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err("Entry distribution [" . $data->getEntryDistributionId() . "] not found");
			return $dbBatchJob;
		}
		
		$providerData = $data->getProviderData();
		if($providerData instanceof kUverseDistributionJobProviderData)
		{
			KalturaLog::debug('Updating uverse job provider data in entry distribution custom data');
			$entryDistribution->putInCustomData(UverseEntryDistributionCustomDataField::REMOTE_ASSET_URL, $providerData->getRemoteAssetUrl());
			$entryDistribution->putInCustomData(UverseEntryDistributionCustomDataField::REMOTE_ASSET_FILE_NAME, $providerData->getRemoteAssetFileName());
			$entryDistribution->save();
		}
		
		return $dbBatchJob;
	}
}