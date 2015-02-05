<?php
/**
 * @package plugins.integration
 * @subpackage Scheduler
 */
class KAsyncIntegrate extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::INTEGRATION;
	}
	
	/**
	 * Static helper function to retrieve the notification URL from any engine
	 * @return string
	 */
	public static function getCallbackNotificationUrl()
	{
		$job = self::getCurrentJob();
		$data = $job->data;
		/* @var $data KalturaIntegrationJobData */
		return $data->callbackNotificationBaseUrl . "/partnerId/{$job->partnerId}/id/{$job->id}";
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->integrate($job, $job->data);
	}
	
	protected function integrate(KalturaBatchJob $job, KalturaIntegrationJobData $data)
	{
		KalturaLog::debug("integrate($job->id)");
		
		$engine = $this->getEngine($job->jobSubType);
		if(!$engine)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::ENGINE_NOT_FOUND, "Engine not found", KalturaBatchJobStatus::FAILED);
		}
		
		$this->impersonate($job->partnerId);
		$finished = $engine->dispatch($job, $data);
		$this->unimpersonate();
		
		if(!$finished)
		{
			return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE, $data);
		}
		
		return $this->closeJob($job, null, null, "Integrated", KalturaBatchJobStatus::FINISHED, $data);
	}

	/**
	 * @param KalturaIntegrationProviderType $type
	 * @return KIntegrationEngine
	 */
	protected function getEngine($type)
	{
		return KalturaPluginManager::loadObject('KIntegrationEngine', $type);
	}
}
