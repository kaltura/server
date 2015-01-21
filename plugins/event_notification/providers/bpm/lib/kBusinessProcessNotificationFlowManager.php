<?php
/**
 * @package plugins.businessProcessNotification
 */
class kBusinessProcessNotificationFlowManager implements kBatchJobStatusEventConsumer, kObjectDeletedEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getJobType() != EventNotificationPlugin::getBatchJobTypeCoreValue(EventNotificationBatchType::EVENT_NOTIFICATION_HANDLER))
			return false;
			
		if($dbBatchJob->getJobSubType() != BusinessProcessNotificationPlugin::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_START))
			return false;
			
		if($dbBatchJob->getStatus() != BatchJob::BATCHJOB_STATUS_FINISHED)
			return false;
			
		return true;	
	}
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		$this->onBusinessProcessStart($dbBatchJob, $dbBatchJob->getData());
		return true;
	}
	
	private function onBusinessProcessStart(BatchJob $dbBatchJob, kBusinessProcessNotificationDispatchJobData $data)
	{
		$object = $data->getObject();
		$template = EventNotificationTemplatePeer::retrieveByPK($data->getTemplateId());
		if($template instanceof BusinessProcessNotificationTemplate)
		{
			$template->setCaseId($object, $data->getCaseId());
		}
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		$entryId = null;		
		if($object instanceof entry)
			$entryId = $object->getId();
		elseif(method_exists($object, 'getEntryId'))
			$entryId = $object->getEntryId();
			
		$partnerId = null;
		if(method_exists($object, 'getPartnerId'))
			$partnerId = $object->getPartnerId();
			
		$abortCaseJobType = BusinessProcessNotificationPlugin::getBusinessProcessNotificationTemplateTypeCoreValue(BusinessProcessNotificationTemplateType::BPM_ABORT);
		
		$scope = new kEventNotificationScope();
		$scope->setObject($object);
		if($raisedJob)
			$scope->setParentRaisedJob($raisedJob);
		
		$cases = BusinessProcessNotificationTemplate::getCases($object);
		foreach($cases as $case)
		{
			$notificationTemplate = EventNotificationTemplatePeer::retrieveByPK($case['templateId']);
			/* @var $notificationTemplate BusinessProcessStartNotificationTemplate */
			if($notificationTemplate->getPartnerId())
				$partnerId = $notificationTemplate->getPartnerId();
				
			$scope->setPartnerId($partnerId);
			$jobData = $notificationTemplate->getJobData($scope);
			kEventNotificationFlowManager::addEventNotificationDispatchJob($abortCaseJobType, $jobData, $partnerId, $entryId, $raisedJob);
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		$cases = BusinessProcessNotificationTemplate::getCases($object);
		if($cases)
			return true;
			
		return false;
	}
}