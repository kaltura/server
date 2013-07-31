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
			
			$scope = $event->getScope();
			$type = $notificationTemplate->getType();
			$jobData = $notificationTemplate->getJobData($scope);
			self::addEventNotificationDispatchJob($type, $jobData, $scope->getPartnerId(), $entryId, $parentJob);
		}
		return true;
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
	 * @return string class name
	 */
	protected function getEventObjectType(KalturaEvent $event) 
	{
		if($event instanceof kBatchJobStatusEvent)
			return 'BatchJob';
			
		if(!method_exists($event, 'getObject'))
			return null;
			
		$object = $event->getObject();
		return get_class($object);
	}

	/**
	 * @param EventNotificationTemplate $notificationTemplate
	 * @param kEventScope $scope
	 * @return boolean
	 */
	protected function notificationTemplatesConditionsFulfilled(EventNotificationTemplate $notificationTemplate, kEventScope $scope) 
	{
		$eventConditions = $notificationTemplate->getEventConditions();
		if(!$eventConditions || !count($eventConditions))
			return true;
		
		foreach($eventConditions as $eventCondition)
		{
			/* @var $eventCondition kCondition */
			if(!$eventCondition->fulfilled($scope))
				return false;
		}
		
		return true;
	}

	/**
	 * @param int $eventType
	 * @param string $eventObjectClassName core class name
	 * @param int $partnerId
	 * @return array<EventNotificationTemplate>
	 */
	public static function getNotificationTemplates($eventType, $eventObjectClassName, $partnerId)
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
			if(!$notificationTemplate->getAutomaticDispatchEnabled())
				continue;				
		
			if($notificationTemplate->getEventType() != $eventType)
				continue;				
			
			$templateObjectClassName = KalturaPluginManager::getObjectClass('EventNotificationEventObjectType', $notificationTemplate->getObjectType());
			if(strcmp($eventObjectClassName, $templateObjectClassName) && !is_subclass_of($eventObjectClassName, $templateObjectClassName))
				continue;				
			
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
		if($scope->getPartnerId() <= 0 || !EventNotificationPlugin::isAllowedPartner($scope->getPartnerId()))
			return false;
			
		$eventType = self::getEventType($event);
		$eventObjectClassName = self::getEventObjectType($event);
		
		$notificationTemplates = self::getNotificationTemplates($eventType, $eventObjectClassName, $scope->getPartnerId());
		if(!count($notificationTemplates))
			return false;
			
		foreach($notificationTemplates as $notificationTemplate)
		{
			/* @var $notificationTemplate EventNotificationTemplate */
			
			$scope->resetDynamicValues();
			
			$notificationParameters = $notificationTemplate->getContentParameters();
			foreach($notificationParameters as $notificationParameter)
			{
				/* @var $notificationParameter kEventNotificationParameter */
				$scope->addDynamicValue($notificationParameter->getKey(), $notificationParameter->getValue());
			}
			
			$notificationParameters = $notificationTemplate->getUserParameters();
			foreach($notificationParameters as $notificationParameter)
			{
				/* @var $notificationParameter kEventNotificationParameter */
				$scope->addDynamicValue($notificationParameter->getKey(), $notificationParameter->getValue());
			}
			
			if($this->notificationTemplatesConditionsFulfilled($notificationTemplate, $scope))
				$this->notificationTemplates[] = $notificationTemplate;
		}
		
		return count($this->notificationTemplates);
	}
}
