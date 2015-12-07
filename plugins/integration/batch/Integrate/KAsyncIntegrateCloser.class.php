<?php
/**
 * @package plugins.integration
 * @subpackage Scheduler
 */
class KAsyncIntegrateCloser extends KJobCloserWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::INTEGRATION;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->close($job, $job->data);
	}
	
	protected function close(KalturaBatchJob $job, KalturaIntegrationJobData $data)
	{
		KalturaLog::debug("close($job->id)");
		
		if(($job->queueTime + self::$taskConfig->params->maxTimeBeforeFail) < time())
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
		}
		
		$engine = $this->getEngine($job->jobSubType);
		if(!$engine)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::ENGINE_NOT_FOUND, "Engine not found", KalturaBatchJobStatus::FAILED);
		}
		
		$this->impersonate($job->partnerId);
		$finished = $engine->close($job, $data);
		$this->unimpersonate();
		
		if(!$finished)
		{
			return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE, $data);
		}
		
		return $this->closeJob($job, null, null, "Integrated", KalturaBatchJobStatus::FINISHED, $data);
	}

	/**
	 * @param KalturaIntegrationProviderType $type
	 * @return KIntegrationCloserEngine
	 */
	protected function getEngine($type)
	{
		return KalturaPluginManager::loadObject('KIntegrationCloserEngine', $type);
	}
}
