<?php
/**
 * @package plugins.eventNotification
 * @subpackage lib
 */
class kEventNotificationFlowManager implements kGenericEventConsumer
{
	static protected $allNotificationTemplates = null;
	
	/**
	 * @var array<EventNotificationTemplate>
	 */
	protected $notificationTemplates;
	
	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::consumeEvent()
	 */
	public function consumeEvent(KalturaEvent $event) 
	{
	    if (EventNotificationPlugin::isAllowedPartner($event->getScope()->getPartnerId()))
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
    			$jobData = $notificationTemplate->getJobData($event->getScope());
    			self::addEventNotificationDispatchJob($type, $jobData, null, $entryId, $parentJob);
    		}
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
			if (!$partnerId)
			{
			    $partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			}
			$batchJob->setPartnerId($partnerId ? $partnerId : kCurrentContext::$partner_id);
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
	protected function getEventType(KalturaEvent $event) 
	{
		$matches = null;
		if(!preg_match('/k(\w+)Event/', get_class($event), $matches))
			return null;
			
		$typeName = $matches[1];
		$constName = strtoupper(preg_replace('/(?!^)[[:upper:]]/','_\0', $typeName));
		if(defined("EventNotificationEventType::{$constName}"))
		{
			$type = constant("EventNotificationEventType::{$constName}");
			if($type)
				return $type;
		}
			
		return DynamicEnumPeer::retrieveValueByEnumValueName('EventNotificationEventType', $constName);
	}

	/**
	 * Return single integer value that represents the event object type
	 * @param KalturaEvent $event
	 * @return int
	 */
	protected function getEventObjectType(KalturaEvent $event) 
	{
		if($event instanceof kBatchJobStatusEvent)
			return EventNotificationEventObjectType::BATCHJOB;
			
		if(!method_exists($event, 'getObject'))
			return null;
			
		$object = $event->getObject();
		$constName = strtoupper(get_class($object));
		if(defined("EventNotificationEventObjectType::{$constName}"))
		{
			$type = constant("EventNotificationEventObjectType::{$constName}");
			if($type)
				return $type;
		}
			
		return DynamicEnumPeer::retrieveValueByEnumValueName('EventNotificationEventObjectType', $constName);
	}

	/**
	 * @param EventNotificationTemplate $notificationTemplate
	 * @param kEventScope $scope
	 * @return boolean
	 */
	protected function notificationTemplatesConditionsFulfilled(EventNotificationTemplate $notificationTemplate, kEventScope $scope) 
	{
	    KalturaLog::info("Checking conditions for notification template ID [". $notificationTemplate->getId() ."] and scope: ". print_r($scope, true));
		$eventConditions = $notificationTemplate->getEventConditions();
		if(!$eventConditions || !count($eventConditions))
			return true;
		
		foreach($eventConditions as $eventCondition)
		{
			/* @var $eventCondition kEventCondition */
			if(!$eventCondition->fulfilled($scope))
			{
				KalturaLog::debug("Template [" . $notificationTemplate->getId() . "] condition not fulfilled");
				return false;
			}
		}
		
		return true;
	}

	/**
	 * @param int $eventType
	 * @param int $eventObjectType
	 * @param int $partnerId
	 * @return array<EventNotificationTemplate>
	 */
	public static function getNotificationTemplates($eventType, $eventObjectType, $partnerId)
	{
		if(is_null(self::$allNotificationTemplates))
		{
			self::$allNotificationTemplates = EventNotificationTemplatePeer::retrieveByPartnerId($partnerId);
			KalturaLog::debug("Found [" . count(self::$allNotificationTemplates) . "] templates");
		}
		
		$notificationTemplates = array();
		foreach(self::$allNotificationTemplates as $notificationTemplate)
		{
			/* @var $notificationTemplate EventNotificationTemplate */
			if(	$notificationTemplate->getEventType() == $eventType && 
				$notificationTemplate->getObjectType() == $eventObjectType &&
				$notificationTemplate->getAutomaticDispatchEnabled()
				)
					$notificationTemplates[] = $notificationTemplate;
		}
		return $notificationTemplates;
	}
		
	/* (non-PHPdoc)
	 * @see kGenericEventConsumer::shouldConsumeEvent()
	 */
	public function shouldConsumeEvent(KalturaEvent $event) 
	{
		$this->notificationTemplates = array();
		
		$scope = $event->getScope();
		if($scope->getPartnerId() <= 0)
			return;
			
		$eventType = self::getEventType($event);
		$eventObjectType = self::getEventObjectType($event);
		
		$notificationTemplates = self::getNotificationTemplates($eventType, $eventObjectType, $scope->getPartnerId());
		if(!count($notificationTemplates))
			return false;
			
		foreach($notificationTemplates as $notificationTemplate)
		{
			/* @var $notificationTemplate EventNotificationTemplate */
			if($this->notificationTemplatesConditionsFulfilled($notificationTemplate, $scope))
				$this->notificationTemplates[] = $notificationTemplate;
		}
		
		return count($this->notificationTemplates);
	}
}
