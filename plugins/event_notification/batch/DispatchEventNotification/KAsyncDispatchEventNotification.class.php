<?php
/**
 * @package plugins.eventNotification
 * @subpackage Scheduler
 */
class KAsyncDispatchEventNotification extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::EVENT_NOTIFICATION_HANDLER;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->dispatch($job, $job->data);
	}
	
	protected function dispatch(KalturaBatchJob $job, KalturaEventNotificationDispatchJobData $data)
	{
		KalturaLog::debug("dispatch($job->id)");
		
		$this->updateJob($job, "Dispatch template [$data->templateId]", KalturaBatchJobStatus::QUEUED);
		
		$eventNotificationPlugin = KalturaEventNotificationClientPlugin::get(self::$kClient);
		$eventNotificationTemplate = $eventNotificationPlugin->eventNotificationTemplate->get($data->templateId);
		
		$engine = $this->getEngine($job->jobSubType);
		if(!$engine)
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::ENGINE_NOT_FOUND, "Engine not found", KalturaBatchJobStatus::FAILED);
		
		$this->impersonate($job->partnerId);
		$engine->dispatch($eventNotificationTemplate, $data);
		$this->unimpersonate();
		
		return $this->closeJob($job, null, null, "Dispatched", KalturaBatchJobStatus::FINISHED, $data);
	}

	/**
	 * @param KalturaEventNotificationTemplateType $type
	 * @return KDispatchEventNotificationEngine
	 */
	protected function getEngine($type)
	{
		return KalturaPluginManager::loadObject('KDispatchEventNotificationEngine', $type);
	}
}
