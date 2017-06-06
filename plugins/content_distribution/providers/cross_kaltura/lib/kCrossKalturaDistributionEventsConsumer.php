<?php
/**
 * @package plugins.crossKalturaDistribution
 * @subpackage lib
 */
class kCrossKalturaDistributionEventsConsumer implements kBatchJobStatusEventConsumer
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
		
	    if(!in_array($dbBatchJob->getJobType(), $jobTypes))
	    {	
            // wrong job type
			return false;
		}
	    
	    $data = $dbBatchJob->getData();
		if (!$data instanceof kDistributionJobData)
		{	
		    KalturaLog::err('Wrong job data type');
			return false;
		}	
		
		$crossKalturaCoreValueType = kPluginableEnumsManager::apiToCore('DistributionProviderType', CrossKalturaDistributionPlugin::getApiValue(CrossKalturaDistributionProviderType::CROSS_KALTURA));
		if ($data->getProviderType() == $crossKalturaCoreValueType)
		{		
			return true;
		}		
		
		// not the right provider
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob, BatchJob $twinJob = null)
	{		
		if ($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{				
			return self::onDistributionJobFinished($dbBatchJob);
		}
		
		return true;
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @return BatchJob
	 */
	public static function onDistributionJobFinished(BatchJob $dbBatchJob)
	{
	    $data = $dbBatchJob->getData();
	    
		$entryDistribution = EntryDistributionPeer::retrieveByPK($data->getEntryDistributionId());
		if(!$entryDistribution)
		{
			KalturaLog::err('Entry distribution ['.$data->getEntryDistributionId().'] not found');
			return $dbBatchJob;
		}
		
		$providerData = $data->getProviderData();
		if(!($providerData instanceof kCrossKalturaDistributionJobProviderData))
		{
		    KalturaLog::err('Wrong provider data class ['.get_class($providerData).']');
			return $dbBatchJob;
		}
		
		$entryDistribution->putInCustomData(CrossKalturaDistributionCustomDataField::DISTRIBUTED_FLAVOR_ASSETS, $providerData->getDistributedFlavorAssets());
		$entryDistribution->putInCustomData(CrossKalturaDistributionCustomDataField::DISTRIBUTED_THUMB_ASSETS, $providerData->getDistributedThumbAssets());
		$entryDistribution->putInCustomData(CrossKalturaDistributionCustomDataField::DISTRIBUTED_METADATA, $providerData->getDistributedMetadata());
		$entryDistribution->putInCustomData(CrossKalturaDistributionCustomDataField::DISTRIBUTED_CAPTION_ASSETS, $providerData->getDistributedCaptionAssets());
		$entryDistribution->putInCustomData(CrossKalturaDistributionCustomDataField::DISTRIBUTED_CUE_POINTS, $providerData->getDistributedCuePoints());
		$entryDistribution->putInCustomData(CrossKalturaDistributionCustomDataField::DISTRIBUTED_THUMB_CUE_POINTS, $providerData->getDistributedThumbCuePoints());
		$entryDistribution->putInCustomData(CrossKalturaDistributionCustomDataField::DISTRIBUTED_TIMED_THUMB_ASSETS, $providerData->getDistributedTimedThumbAssets());
		$entryDistribution->save();
		
		return $dbBatchJob;
	}
	
}