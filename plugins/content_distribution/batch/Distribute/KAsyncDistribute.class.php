<?php
require_once("bootstrap.php");
/**
 * Distributes kaltura entries to remote destination  
 *
 * @package Scheduler
 * @subpackage Distribute
 */
abstract class KAsyncDistribute extends KBatchBase
{
	/**
	 * @return array<KalturaBatchJob>
	 */
	abstract protected function getExclusiveDistributeJobs();
	
	/**
	 * @return DistributionEngine
	 */
	abstract protected function getDistributionEngine($providerType);
	
	public function run($jobs = null)
	{
		KalturaLog::info("Distribute batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		if(is_null($jobs))
			$jobs = $this->getExclusiveDistributeJobs();
		
		KalturaLog::info(count($jobs) . " Distribute jobs to perform");
		
		if(! count($jobs) > 0)
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return null;
		}
		
		foreach($jobs as &$job)
			$job = $this->distribute($job, $job->data);
			
		return $jobs;
	}
	
	protected function distribute(KalturaBatchJob $job, KalturaDistributionJobData $data)
	{
		KalturaLog::debug("distribute($job->id)");
		
		try
		{
			$engine = $this->getDistributionEngine($job->jobSubType);
			if (!$engine)
			{
				KalturaLog::err('Cannot create DistributeEngine of type ['.$job->jobSubType.']');
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, null, 'Error: Cannot create DistributeEngine of type ['.$job->jobSubType.']', KalturaBatchJobStatus::FAILED);
				return $job;
			}
						
			// configure engine
			if (!$engine->config($this->taskConfig->params))
			{
				KalturaLog::err('Cannot configure DistributeEngine of type ['.$job->jobSubType.']');
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, null, 'Error: Cannot configure DistributeEngine of type ['.$job->jobSubType.']', KalturaBatchJobStatus::FAILED);
				return $job;
			}
			
			$closed = $engine->execute();
			if($closed)
				return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
			 			
			return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE);
		}
		catch(Exception $ex)
		{
			$job = $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED, KalturaEntryStatus::ERROR_CONVERTING, $job->data);
		}
		return $job;
	}
}
