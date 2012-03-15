<?php
/**
 * @package plugins.eventNotification
 * @subpackage lib
 */
class kEventNotificationFlowManager implements kGenericEventConsumer
{
	/**
	 * @var array<EventNotificationTemplate>
	 */
	protected $notificationTemplates;
	
	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::consumeEvent()
	 */
	public function consumeEvent(KalturaEvent $event) 
	{
		foreach($this->notificationTemplates as $notificationTemplate)
		{
			/* @var $notificationTemplates EventNotificationTemplate */
			
			// TODO create the job data
		}
	}

	/**
	 * @param int $eventNotificationType
	 * @param kEventNotificationDispatchJobData $jobData
	 * @param string $partnerId
	 * @param string $entryId
	 * @param BatchJob $parentJob
	 * @return BatchJob
	 */
	public static function addEventNotificationDispatchJob($eventNotificationType, kEventNotificationDispatchJobData $jobData, $partnerId, $entryId = null, BatchJob $parentJob = null) 
	{
		$batchJob = null;
		
		if ($parentJob)
		{
			$batchJob = $parentJob->createChild(false);
		}
		else
		{
			$batchJob = new BatchJob();
			$batchJob->setEntryId($entryId);
			$batchJob->setPartnerId($partnerId);
		}
		
		KalturaLog::log("Creating event notification dispatch job on template id [" . $jobData->getTemplateId() . "] engine[$eventNotificationType]");
		
		$jobType = EventNotificationPlugin::getBatchJobTypeCoreValue(EventNotificationBatchType::EVENT_NOTIFICATION_HANDLER); 
		return kJobsManager::addJob($batchJob, $jobData, $jobType, $eventNotificationType);
	}

	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::shouldConsumeEvent()
	 */
	public function shouldConsumeEvent(KalturaEvent $event) 
	{
		$this->notificationTemplates = array();
		
		// TODO implement $event->getType(), $event->getObjectType() in all event objects? or make them strings?
		
		$notificationTemplates = EventNotificationTemplatePeer::retrieveByEventType($event->getType(), $event->getObjectType());
		foreach($notificationTemplates as $notificationTemplate)
		{
			/* @var $notificationTemplates EventNotificationTemplate */
			
			$eventConditions = $notificationTemplates->getEventConditions();
			if(!$eventConditions)
				return true;
				
			$fulfilled = true;
			foreach($eventConditions as $eventCondition)
			{
				// TODO - how to implement kEventCondition?
				
				/* @var $eventCondition kEventCondition */
				if(!$eventCondition->fulfilled($event))
				{
					$fulfilled = false;
					break;
				}
			}
			
			if($fulfilled)
				return true;
		}
		
		return false;
	}
}
