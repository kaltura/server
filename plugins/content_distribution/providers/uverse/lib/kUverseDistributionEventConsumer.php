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
			case BatchJob::BATCHJOB_STATUS_FINISHED:
				return self::onDistributionJobFinished($dbBatchJob, $data);
			default:
				return $dbBatchJob;
		}
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kDistributionJobData $data
	 * @return BatchJob
	 */
	public static function onDistributionJobFinished(BatchJob $dbBatchJob, kDistributionJobData $data)
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
			$entryDistribution->putInCustomData(UverseEntryDistributionCustomDataField::REMOTE_ASSET_URL, $providerData->getRemoteAssetUrl());
			$entryDistribution->putInCustomData(UverseEntryDistributionCustomDataField::REMOTE_ASSET_FILE_NAME, $providerData->getRemoteAssetFileName());
			$entryDistribution->save();
		}
		
		return $dbBatchJob;
	}
}