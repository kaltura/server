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
		if(KBatchBase::$taskConfig->isInitOnly())
			return $this->init();
		
		if(is_null($jobs))
			$jobs = KBatchBase::$kClient->batch->getExclusiveAlmostDone($this->getExclusiveLockKey(), KBatchBase::$taskConfig->maximumExecutionTime, $this->getMaxJobsEachRun(), $this->getFilter(), static::getType());
		
		KalturaLog::info(count($jobs) . " jobs to close");
		
		if(! count($jobs) > 0)
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(static::getType(), 0);
			return null;
		}
		
		foreach($jobs as &$job)
		{
			try
			{
				self::setCurrentJob($job);
				$job = $this->exec($job);
			}
			catch(KalturaException $kex)
			{
				KBatchBase::unimpersonate();
				$job = $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_API, $kex->getCode(), "Error: " . $kex->getMessage(), KalturaBatchJobStatus::FAILED);
			}
			catch(KalturaClientException $kcex)
			{
				KBatchBase::unimpersonate();
				$job = $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_CLIENT, $kcex->getCode(), "Error: " . $kcex->getMessage(), KalturaBatchJobStatus::RETRY);
			}
			catch(Exception $ex)
			{
				KBatchBase::unimpersonate();
				$job = $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
			}
			self::unsetCurrentJob();
		}
			
		return $jobs;
	}
	
	/**
	* @param string $jobType
	* @return KalturaWorkerQueueFilter
	*/
	protected function getQueueFilter($jobType)
	{
		$workerQueueFilter = $this->getBaseQueueFilter($jobType);
		$workerQueueFilter->filter->statusEqual = KalturaBatchJobStatus::ALMOST_DONE;
		
		return $workerQueueFilter;
	}
}
