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

		$objectType = BatchJobObjectType::ENTRY;
		$objectId = null;
		if($scope instanceof kEventScope)
		{
			$event = $scope->getEvent();
			if($event instanceof kApplicativeEvent)
			{
				$parentJob = $event->getRaisedJob();
			}
		
			$object = $scope->getObject();
			$objectId = $scope->getObject() ? $scope->getObject()->getId() : null;
			if($object instanceof entry)
			{
				$entryId = $object->getId();
			}
			elseif(method_exists($object, 'getEntryId'))
			{
				$entryId = $object->getEntryId();
			}

			switch (get_class($object))
			{
				case 'entry':
					$objectType = BatchJobObjectType::ENTRY;
					break;
				case 'category':
					$objectType = BatchJobObjectType::CATEGORY;
					break;
				case'kuser':
					$objectType = BatchJobObjectType::USER;
					break;
				default:
					$objectType = BatchJobObjectType::ENTRY;
					if ($object instanceof asset)
					{
						$objectType = BatchJobObjectType::ASSET;
					}

					break;
			}
		}
		
		if(!$eventNotificationType)
		{
			$eventNotificationType = $this->getType();
		}
		
		$job = $this->addEventNotificationDispatchJob($eventNotificationType, $jobData, $scope->getPartnerId(), $entryId, $parentJob, $objectId, $objectType);
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
	protected function addEventNotificationDispatchJob($eventNotificationType, kEventNotificationDispatchJobData $jobData, $partnerId = null, $entryId = null, BatchJob $parentJob = null, $objectId = null, $objectType = BatchJobObjectType::ENTRY)
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

		$batchJob->setObjectId($objectId);
		$batchJob->setObjectType($objectType);
		$batchJob->setStatus(BatchJob::BATCHJOB_STATUS_DONT_PROCESS);
		
		$batchJob = kJobsManager::addJob($batchJob, $jobData, $jobType, $eventNotificationType);
		$jobData->setJobId($batchJob->getId());
		$batchJob->setData($jobData);

		if ($this->getEventDelayedConditions() && $entryId && $this->areDelayedEventConditionsMet($entryId))
		{
			return kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_DELAYED);
		}

		return kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_PENDING);
	}

	public function areDelayedEventConditionsMet($objectId)
	{
		$delayedEventConditions = $this->getEventDelayedConditions();
		if ($delayedEventConditions)
		{
			switch ($delayedEventConditions)
			{
				case EventNotificationDelayedConditions::PENDING_ENTRY_READY:
				{
					$entry = BaseentryPeer::retrieveByPK($objectId);
					return $entry->getStatus() !== entryStatus::READY;
				}
			}
		}
		return false;
	}
}
