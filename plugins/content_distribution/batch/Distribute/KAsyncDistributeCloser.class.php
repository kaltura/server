<?php
/**
 * Closes asynchronous distribution jobs
 *
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
abstract class KAsyncDistributeCloser extends KJobCloserWorker
{
	/**
	 * @var IDistributionEngine
	 */
	protected $engine;
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->distribute($job, $job->data);
	}
	
	/**
	 * @return DistributionEngine
	 */
	abstract protected function getDistributionEngine($providerType, KalturaDistributionJobData $data);
	
	/**
	 * Throw detailed exceptions for any failure 
	 * @return bool true if job is closed, false for almost done
	 */
	abstract protected function execute(KalturaDistributionJobData $data);
	
	protected function distribute(KalturaBatchJob $job, KalturaDistributionJobData $data)
	{
		
		if(($job->queueTime + self::$taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
		
		try
		{
			$this->engine = $this->getDistributionEngine($job->jobSubType, $data);
			if (!$this->engine)
			{
				KalturaLog::err('Cannot create DistributeEngine of type ['.$job->jobSubType.']');
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, null, 'Error: Cannot create DistributeEngine of type ['.$job->jobSubType.']', KalturaBatchJobStatus::FAILED);
				return $job;
			}
			$job = $this->updateJob($job, "Engine found [" . get_class($this->engine) . "]", KalturaBatchJobStatus::QUEUED);
						
			$closed = $this->execute($data);
			if($closed)
				return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED, $data);
			 			
			return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE, $data);
		}
		catch(KalturaDistributionException $ex)
		{
			$job = $this->closeJob($job, KalturaBatchJobErrorTypes::APP, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::RETRY, $job->data);
		}
		catch(Exception $ex)
		{
			$job = $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED, $job->data);
		}
		return $job;
	}
}
