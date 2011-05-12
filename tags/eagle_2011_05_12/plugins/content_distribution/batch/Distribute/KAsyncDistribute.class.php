<?php
require_once("bootstrap.php");
/**
 * Distributes kaltura entries to remote destination  
 *
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
abstract class KAsyncDistribute extends KBatchBase
{
	/**
	 * Enter description here ...
	 * @var IDistributionEngine
	 */
	protected $engine;
	
	/**
	 * @return array<KalturaBatchJob>
	 */
	abstract protected function getExclusiveDistributeJobs();
	
	/**
	 * @return DistributionEngine
	 */
	abstract protected function getDistributionEngine($providerType, KalturaDistributionJobData $data);
	
	/**
	 * Throw detailed exceptions for any failure 
	 * @return bool true if job is closed, false for almost done
	 */
	abstract protected function execute(KalturaDistributionJobData $data);
	
	/**
	 * Saves the typed queue to for the scheduler
	 */
	abstract protected function saveEmptyQueue();
	
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
			$this->saveEmptyQueue();
			return null;
		}
		
		foreach($jobs as &$job)
			$job = $this->distribute($job, $job->data);
			
		return $jobs;
	}
	
	protected function distribute(KalturaBatchJob $job, KalturaDistributionJobData $data)
	{
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
