<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage Scheduler
 */
class KDispatchBusinessProcessNotificationEngine extends KDispatchEventNotificationEngine
{
	/* (non-PHPdoc)
	 * @see KDispatchEventNotificationEngine::dispatch()
	 */
	public function dispatch(KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationDispatchJobData &$data)
	{
		$job = KJobHandlerWorker::getCurrentJob();
	
		$variables = array();
		if(is_array($data->contentParameters) && count($data->contentParameters))
		{
			foreach($data->contentParameters as $contentParameter)
			{
				/* @var $contentParameter KalturaKeyValue */
				$variables[$contentParameter->key] = $contentParameter->value;
			}		
		}
		
		switch ($job->jobSubType)
		{
			case KalturaEventNotificationTemplateType::BPM_START:
				return $this->startBusinessProcess($eventNotificationTemplate, $data, $variables);
				
			case KalturaEventNotificationTemplateType::BPM_SIGNAL:
				return $this->signalCase($eventNotificationTemplate, $data, $variables);
				
			case KalturaEventNotificationTemplateType::BPM_ABORT:
				return $this->abortCase($eventNotificationTemplate, $data);
		}
	}

	/**
	 * @param KalturaBusinessProcessStartNotificationTemplate $template
	 * @param KalturaBusinessProcessNotificationDispatchJobData $data
	 */
	public function startBusinessProcess(KalturaBusinessProcessStartNotificationTemplate $template, KalturaBusinessProcessNotificationDispatchJobData &$data, $variables)
	{	
		$provider = kBusinessProcessProvider::get($data->server);
		KalturaLog::debug("Starting business-process [{$template->processId}] with variables [" . print_r($variables, true) . "]");
		$data->caseId = $provider->startBusinessProcess($template->processId, $variables);
		KalturaLog::debug("Started business-process case [{$data->caseId}]");
	}

	/**
	 * @param KalturaBusinessProcessSignalNotificationTemplate $template
	 * @param KalturaBusinessProcessNotificationDispatchJobData $data
	 */
	public function signalCase(KalturaBusinessProcessSignalNotificationTemplate $template, KalturaBusinessProcessNotificationDispatchJobData &$data, $variables)
	{
		$provider = kBusinessProcessProvider::get($data->server);
		KalturaLog::debug("Signaling business-process [{$template->processId}] case [{$data->caseId}] with message [{$template->message}] on blocking event [{$template->eventId}]");
		$provider->signalCase($data->caseId, $template->eventId, $template->message, $variables);
	}

	/**
	 * @param KalturaBusinessProcessStartNotificationTemplate $template
	 * @param KalturaBusinessProcessNotificationDispatchJobData $data
	 */
	public function abortCase(KalturaBusinessProcessStartNotificationTemplate $template, KalturaBusinessProcessNotificationDispatchJobData &$data)
	{
		$provider = kBusinessProcessProvider::get($data->server);
		KalturaLog::debug("Aborting business-process [{$template->processId}] case [{$data->caseId}]");
		$provider->abortCase($data->caseId);
	}
}
