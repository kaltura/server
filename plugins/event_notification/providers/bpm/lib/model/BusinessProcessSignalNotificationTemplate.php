<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage model
 */
class BusinessProcessSignalNotificationTemplate extends BusinessProcessNotificationTemplate
{
	const CUSTOM_DATA_MESSAGE = 'message';
	const CUSTOM_DATA_EVENT_ID = 'eventId';
	
	public function __construct()
	{
		$this->setType(BusinessProcessNotificationPlugin::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_SIGNAL));
		parent::__construct();
	}
	
	/* (non-PHPdoc)
	 * @see BatchEventNotificationTemplate::dispatch()
	 */
	public function dispatch(kScope $scope)
	{
		$jobData = $this->getJobData($scope);
		/* @var $jobData kBusinessProcessNotificationDispatchJobData */
		if(!$jobData->getObject())
		{
			return;
		}

		// check if event template is dispatching from a batch job event, if so, try to get the right case to signal
		$currentCaseId = null;
		if ($scope instanceof kEventScope && $scope->getEvent() instanceof IKalturaBatchJobRelatedEvent)
		{
			/** @var IKalturaBatchJobRelatedEvent $currentEvent */
			$currentEvent = $scope->getEvent();
			$eventBatchJob = $currentEvent->getBatchJob();
			$eventJobData = $eventBatchJob->getData();
			// kIntegrationJobData is not one of our dependant plugins
			if (class_exists('kIntegrationJobData') && $eventJobData instanceof kIntegrationJobData)
			{
				$eventJobTriggerData = $eventJobData->getTriggerData();
				if ($eventJobTriggerData instanceof IBusinessProcessCaseIdRelated)
					$currentCaseId = $eventJobTriggerData->getCaseId();
			}
		}

		$caseIds = $this->getCaseIds($jobData->getObject());
		$jobId = null;
		foreach($caseIds as $caseId)
		{
			// when we have $currentCaseId, we should only signal to that case
			if (!is_null($currentCaseId) && $caseId !== $currentCaseId)
				continue;

			$currentJobData = clone $jobData;
			$currentJobData->setCaseId($caseId);
			$this->dispatchJob($scope, $currentJobData);
		}
	}

	public function getMessage()									{return $this->getFromCustomData(self::CUSTOM_DATA_MESSAGE);}
	public function getEventId()									{return $this->getFromCustomData(self::CUSTOM_DATA_EVENT_ID);}
	
	public function setMessage($v)									{return $this->putInCustomData(self::CUSTOM_DATA_MESSAGE, $v);}
	public function setEventId($v)									{return $this->putInCustomData(self::CUSTOM_DATA_EVENT_ID, $v);}
}
