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
			$caseId = $data->getCaseId();
			$template->addCaseId($object, $caseId);
		}
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		$scope = new kEventNotificationScope();
		$scope->setObject($object);
		if($raisedJob)
			$scope->setParentRaisedJob($raisedJob);
		
		$templateIds = BusinessProcessNotificationTemplate::getCaseTemplatesIds($object);
		foreach($templateIds as $templateId)
		{
			$notificationTemplate = EventNotificationTemplatePeer::retrieveByPK($templateId);
			/* @var $notificationTemplate BusinessProcessStartNotificationTemplate */
			
			if(!$notificationTemplate->getAbortOnDeletion())
			{
				continue;
			}
			
			if($notificationTemplate->getPartnerId())
			{
				$scope->setPartnerId($notificationTemplate->getPartnerId());
			}
				
			$notificationTemplate->abort($scope);
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		$cases = BusinessProcessNotificationTemplate::getCaseTemplatesIds($object);
		if($cases)
			return true;
			
		return false;
	}
}