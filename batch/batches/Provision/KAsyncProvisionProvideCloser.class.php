<?php
/**
 * Closes the process of provisioning a new stream.
 *
 * 
 * @package Scheduler
 * @subpackage Provision
 */
class KAsyncProvisionProvideCloser extends KJobCloserWorker
{
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job) {
		$this->closeProvisionProvide($job);
		
	}

	public static function getType()
	{
		return KalturaBatchJobType::PROVISION_PROVIDE;
	}

	protected function closeProvisionProvide (KalturaBatchJob $job)
	{
		if(($job->queueTime + self::$taskConfig->params->maxTimeBeforeFail) < time())
			return new KProvisionEngineResult(KalturaBatchJobStatus::CLOSER_TIMEOUT, "Timed out");
			
		$engine = KProvisionEngine::getInstance( $job->jobSubType, $job->data);
		if ( $engine == null )
		{
			$err = "Cannot find provision engine [{$job->jobSubType}] for job id [{$job->id}]";
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::ENGINE_NOT_FOUND, $err, KalturaBatchJobStatus::FAILED);
		}
		
		KalturaLog::info( "Using engine: " . $engine->getName() );
	
		$results = $engine->checkProvisionedStream($job, $job->data);

		if($results->status == KalturaBatchJobStatus::FINISHED)
			return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED, $results->data);
		
		return $this->closeJob($job, null, null, $results->errMessage, KalturaBatchJobStatus::ALMOST_DONE, $results->data);
		
	}
	
}