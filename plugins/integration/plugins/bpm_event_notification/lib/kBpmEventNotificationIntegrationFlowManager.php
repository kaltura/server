<?php
/**
 * @package plugins.bpmEventNotificationIntegration
 * @subpackage lib.events
 */
class kBpmEventNotificationIntegrationFlowManager implements kBatchJobStatusEventConsumer
{
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		$data = $dbBatchJob->getData();
		/* @var $data kIntegrationJobData */
		
		$triggerData = $data->getTriggerData();
		/* @var $triggerData kBpmEventNotificationIntegrationJobTriggerData */
		
		$template = EventNotificationTemplatePeer::retrieveByPK($triggerData->getTemplateId());
		/* @var $template BusinessProcessNotificationTemplate */
		
		if(!$template)
		{
			KalturaLog::err("Template id [" . $triggerData->getTemplateId() . "] not found");
			return true;
		}
		
		$object = $dbBatchJob->getObject();
		if($object)
		{
			$template->addCaseId($object, $triggerData->getCaseId(), $triggerData->getBusinessProcessId());
		}
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if(		$dbBatchJob->getJobType() == IntegrationPlugin::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION) 
			&&	$dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_DONT_PROCESS 
			&&	$dbBatchJob->getData()->getTriggerType() == BpmEventNotificationIntegrationPlugin::getIntegrationTriggerCoreValue(BpmEventNotificationIntegrationTrigger::BPM_EVENT_NOTIFICATION)
		)
		{
			return true;
		}
				
		return false;
	}

	
}