<?php


/**
 * @package plugins.eventNotification
 * @subpackage model
 */
abstract class BatchEventNotificationTemplate extends EventNotificationTemplate 
{
	/**
	 * Returns job data for dispatching the event notification
	 * @param kScope $scope
	 * @return kEventNotificationDispatchJobData
	 */
	abstract protected function getJobData(kScope $scope = null);
	
	/* (non-PHPdoc)
	 * @see EventNotificationTemplate::dispatch()
	 */
	public function dispatch(kScope $scope)
	{
		$jobData = $this->getJobData($scope);
		return $this->dispatchJob($scope, $jobData);
	}
	
	protected function dispatchJob(kScope $scope, kEventNotificationDispatchJobData $jobData, $eventNotificationType = null)
	{
		$entryId = null;
		$parentJob = null;
		
		if($scope instanceof kEventScope)
		{
			$event = $scope->getEvent();
			if($event instanceof kApplicativeEvent)
			{
				$parentJob = $event->getRaisedJob();
			}
		
			if($event instanceof IKalturaObjectRelatedEvent)
			{
				$object = $event->getObject();
				if($object instanceof entry)
					$entryId = $object->getId();
				elseif(method_exists($object, 'getEntryId'))
					$entryId = $object->getEntryId();
			}
		}
		
		if(!$eventNotificationType)
		{
			$eventNotificationType = $this->getType();
		}
		
		$job = $this->addEventNotificationDispatchJob($eventNotificationType, $jobData, $scope->getPartnerId(), $entryId, $parentJob);
		return $job->getId();
	}


	/**
	 * @param int $eventNotificationType
	 * @param kEventNotificationDispatchJobData $jobData
	 * @param string $partnerId
	 * @param string $entryId
	 * @param BatchJob $parentJob
	 * @return BatchJob
	 */
	protected function addEventNotificationDispatchJob($eventNotificationType, kEventNotificationDispatchJobData $jobData, $partnerId = null, $entryId = null, BatchJob $parentJob = null) 
	{
		$jobType = EventNotificationPlugin::getBatchJobTypeCoreValue(EventNotificationBatchType::EVENT_NOTIFICATION_HANDLER);
		$batchJob = null;
		
		if ($parentJob)
		{
			$batchJob = $parentJob->createChild($jobType, $eventNotificationType, false);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			if (!$partnerId)
				$partnerId = kCurrentContext::getCurrentPartnerId();
				
			$batchJob->setPartnerId($partnerId);
		}
		
		KalturaLog::log("Creating event notification dispatch job on template id [" . $jobData->getTemplateId() . "] engine[$eventNotificationType]");
		
		$batchJob->setObjectId($entryId);
		$batchJob->setObjectType(BatchJobObjectType::ENTRY);
		$batchJob->setStatus(BatchJob::BATCHJOB_STATUS_DONT_PROCESS);
		
		$batchJob = kJobsManager::addJob($batchJob, $jobData, $jobType, $eventNotificationType);
		$jobData->setJobId($batchJob->getId());
		$batchJob->setData($jobData);
		
		return kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_PENDING);
	}
}
