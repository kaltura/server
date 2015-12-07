<?php
/**
 * @package plugins.integration
 * @subpackage lib.events
 */
class kIntegrationFlowManager implements kBatchJobStatusEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		kEventsManager::raiseEvent(new kIntegrationJobClosedEvent($dbBatchJob));
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getJobType() != IntegrationPlugin::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION))
		{
			return false;
		} 
		 
		$closedStatusList = array(
			BatchJob::BATCHJOB_STATUS_FINISHED,
			BatchJob::BATCHJOB_STATUS_FAILED,
			BatchJob::BATCHJOB_STATUS_ABORTED,
			BatchJob::BATCHJOB_STATUS_FATAL,
			BatchJob::BATCHJOB_STATUS_FINISHED_PARTIALLY
		);
		
		return in_array($dbBatchJob->getStatus(), $closedStatusList);
	}
	
	public static function addintegrationJob($objectType, $objectId, kIntegrationJobData $data) 
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($partnerId);
		$batchJob->setObjectType($objectType);
		$batchJob->setObjectId($objectId);
		
		if($objectType == BatchJobObjectType::ENTRY)
		{
			$batchJob->setEntryId($objectId);
		}
		elseif($objectType == BatchJobObjectType::ASSET)
		{
			$asset = assetPeer::retrieveById($objectId);
			if($asset)
				$batchJob->setEntryId($asset->getEntryId());
		}
		
		$batchJob->setStatus(BatchJob::BATCHJOB_STATUS_DONT_PROCESS);
		
		$jobType = IntegrationPlugin::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION);
		$batchJob = kJobsManager::addJob($batchJob, $data, $jobType, $data->getProviderType());
		return kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_PENDING);
	}
}
