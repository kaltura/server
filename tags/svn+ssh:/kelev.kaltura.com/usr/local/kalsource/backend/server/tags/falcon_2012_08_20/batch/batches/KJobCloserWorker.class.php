<?php
/**
 * Base class for all job closer workers.
 * 
 * @package Scheduler
 */
abstract class KJobCloserWorker extends KJobHandlerWorker
{
	public function run($jobs = null)
	{
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		if(is_null($jobs))
			$jobs = $this->kClient->batch->getExclusiveAlmostDone($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, $this->getMaxJobsEachRun(), $this->getFilter(), $this->getJobType());
		
		KalturaLog::info(count($jobs) . " jobs to close");
		
		if(! count($jobs) > 0)
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue($this->getJobType());
			return null;
		}
		
		foreach($jobs as &$job)
		{
			try
			{
				$job = $this->exec($job);
			}
			catch(KalturaException $kex)
			{
				$this->unimpersonate();
				$job = $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_API, $kex->getCode(), "Error: " . $kex->getMessage(), KalturaBatchJobStatus::FAILED);
			}
			catch(KalturaClientException $kcex)
			{
				$this->unimpersonate();
				$job = $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_CLIENT, $kcex->getCode(), "Error: " . $kcex->getMessage(), KalturaBatchJobStatus::RETRY);
			}
			catch(Exception $ex)
			{
				$this->unimpersonate();
				$job = $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
			}
		}
			
		return $jobs;
	}
	
	/**
	* @param string $jobType
	* @param boolean $isCloser
	* @return KalturaWorkerQueueFilter
	*/
	protected function getQueueFilter($jobType)
	{
		$workerQueueFilter = $this->getBaseQueueFilter($jobType);
		$workerQueueFilter->filter->statusEqual = KalturaBatchJobStatus::ALMOST_DONE;
		
		return $workerQueueFilter;
	}
}
