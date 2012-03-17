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
			/* @var $notificationTemplate EventNotificationTemplate */
			
			$parentJob = null;
			if(method_exists($event, 'getRaisedJob'))
			{
				$parentJob = $event->getRaisedJob();
			}
			
			$entryId = null;
			if(method_exists($event, 'getObject'))
			{
				$object = $event->getObject();
				if($object instanceof entry)
					$entryId = $object->getId();
				elseif(method_exists($event, 'getEntryId'))
					$entryId = $object->getEntryId();
			}
			
			$type = $notificationTemplate->getType();
			$jobData = $notificationTemplate->getJobData();
			self::addEventNotificationDispatchJob($type, $jobData, null, $entryId, $parentJob);
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
	public static function addEventNotificationDispatchJob($eventNotificationType, kEventNotificationDispatchJobData $jobData, $partnerId = null, $entryId = null, BatchJob $parentJob = null) 
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
			$batchJob->setPartnerId($partnerId ? $partnerId : kCurrentContext::$ks_partner_id);
		}
		
		KalturaLog::log("Creating event notification dispatch job on template id [" . $jobData->getTemplateId() . "] engine[$eventNotificationType]");
		
		$jobType = EventNotificationPlugin::getBatchJobTypeCoreValue(EventNotificationBatchType::EVENT_NOTIFICATION_HANDLER); 
		return kJobsManager::addJob($batchJob, $jobData, $jobType, $eventNotificationType);
	}

	/**
	 * Return single integer value that represents the event type
	 * @param KalturaEvent $event
	 * @return int
	 */
	public function getEventType(KalturaEvent $event) 
	{
		$matches = null;
		if(!preg_match('/k(\w+)Event/', get_class($event), $matches))
			return null;
			
		$typeName = $matches[1];
		$constName = strtoupper(preg_replace('/(?!^)[[:upper:]]/','_\0', $typeName));
		$type = constant("EventNotificationEventType::{$constName}");
		if($type)
			return $type;
			
		return DynamicEnumPeer::retrieveValueByEnumValueName('EventNotificationEventType', $constName);
	}

	/**
	 * Return single integer value that represents the event object type
	 * @param KalturaEvent $event
	 * @return int
	 */
	public function getEventObjectType(KalturaEvent $event) 
	{
		if($event instanceof kBatchJobStatusEvent)
			return EventNotificationEventObjectType::BATCHJOB;
			
		if(!method_exists($event, 'getObject'))
			return null;
			
		$object = $event->getObject();
		$constName = get_class($object);
		$type = constant("EventNotificationEventObjectType::{$constName}");
		if($type)
			return $type;
			
		return DynamicEnumPeer::retrieveValueByEnumValueName('EventNotificationEventObjectType', $constName);
	}

	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::shouldConsumeEvent()
	 */
	public function shouldConsumeEvent(KalturaEvent $event) 
	{
		$this->notificationTemplates = array();
		
		$eventType = self::getEventType($event);
		$eventObjectType = self::getEventObjectType($event);
		
		$partnerId = kCurrentContext::$ks_partner_id;
		$notificationTemplates = EventNotificationTemplatePeer::retrieveByEventType($eventType, $eventObjectType, $partnerId);
		KalturaLog::debug("Found [" . count($notificationTemplates) . "] templates for event type [$eventType] and object type [$eventObjectType]");
		
		foreach($notificationTemplates as $notificationTemplate)
		{
			/* @var $notificationTemplates EventNotificationTemplate */
			
			if(!$notificationTemplate->getAutomaticDispatchEnabled())
			{
				KalturaLog::notice("Template [" . $notificationTemplate->getId() . "] is not automatic, remove its event type to improve performance");
				continue;
			}
				
			$eventConditions = $notificationTemplates->getEventConditions();
			if(!$eventConditions)
			{
				$this->notificationTemplates[] = $notificationTemplate;
				continue;
			}
				
			$fulfilled = true;
			foreach($eventConditions as $eventCondition)
			{
				// TODO - how to implement kEventCondition?
				
				/* @var $eventCondition kEventCondition */
				if(!$eventCondition->fulfilled($event))
				{
					KalturaLog::debug("Template [" . $notificationTemplate->getId() . "] condition not fulfilled");
					$fulfilled = false;
					break;
				}
			}
			if($fulfilled)
				$this->notificationTemplates[] = $notificationTemplate;
		}
		
		return count($this->notificationTemplates);
	}
}
