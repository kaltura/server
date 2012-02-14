<?php
/**
 * @package plugins.attUverseDistribution
 * @subpackage lib
 */
class kAttUverseDistributionEventConsumer implements kBatchJobStatusEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{		
		$jobTypes = array(
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE)
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
		$data = $dbBatchJob->getData();
		if (!$data instanceof kDistributionJobData)
		{	
			return true;
		}	
		
		$attUverseCoreValueType = kPluginableEnumsManager::apiToCore('DistributionProviderType', AttUverseDistributionPlugin::getApiValue(AttUverseDistributionProviderType::ATT_UVERSE));
		if ($data->getProviderType() != $attUverseCoreValueType)
		{			
			return true;
		}				
		
		$jobTypesToFinish = array(
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_SUBMIT),
			ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_UPDATE)
		);
		if (in_array($dbBatchJob->getJobType(),$jobTypesToFinish) && $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{				
			return self::onDistributionJobFinished($dbBatchJob, $data, $twinJob);
		}
		
		if ($dbBatchJob->getJobType() == ContentDistributionPlugin::getBatchJobTypeCoreValue(ContentDistributionBatchJobType::DISTRIBUTION_DELETE) &&
			$dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_PENDING)
		{			
			kJobsManager::updateBatchJob($dbBatchJob, BatchJob::BATCHJOB_STATUS_FINISHED);
		}
		return true;
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
		if($providerData instanceof kAttUverseDistributionJobProviderData)
		{
			KalturaLog::debug('Updating AttUverse job provider data in entry distribution custom data');
			$entryDistribution->putInCustomData(AttUverseEntryDistributionCustomDataField::REMOTE_ASSET_FILE_URLS, $providerData->getRemoteAssetFileUrls());
			$entryDistribution->putInCustomData(AttUverseEntryDistributionCustomDataField::REMOTE_THUMBNAIL_FILE_URLS, $providerData->getRemoteThumbnailFileUrls());
			$entryDistribution->save();
		}
		
		return $dbBatchJob;
	}
	
}