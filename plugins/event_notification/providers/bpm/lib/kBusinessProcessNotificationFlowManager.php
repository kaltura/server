<?php
/**
 * @package plugins.businessProcessNotification
 */
class kBusinessProcessNotificationFlowManager implements kBatchJobStatusEventConsumer
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

	
}