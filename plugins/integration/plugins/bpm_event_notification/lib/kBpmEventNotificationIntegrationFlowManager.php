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
		
		$template->setCaseId($dbBatchJob, $triggerData->getCaseId());
	}

	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if(		$dbBatchJob->getJobType() == IntegrationPlugin::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION) 
			&&	$dbBatchJob->getStatus() == KalturaBatchJobStatus::BATCHJOB_STATUS_DONT_PROCESS 
			&&	$dbBatchJob->getData()->getTriggerType() == BpmEventNotificationIntegrationPlugin::getIntegrationTriggerCoreValue(BpmEventNotificationIntegrationTrigger::BPM_EVENT_NOTIFICATION)
		)
		{
			return true;
		}
				
		return false;
	}

	
}