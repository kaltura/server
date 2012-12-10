<?php
/**
 * Base class for all job handler workers.
 * 
 * @package Scheduler
 */
abstract class KJobHandlerWorker extends KBatchBase
{
	/**
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob
	 */
	abstract protected function exec(KalturaBatchJob $job);
	
	protected function init()
	{
		$this->saveQueueFilter($this->getJobType());
	}
	
	protected function getMaxJobsEachRun()
	{
		if(!$this->taskConfig->maxJobsEachRun)
			return 1;
		
		return $this->taskConfig->maxJobsEachRun;
	}
	
	protected function getJobs()
	{
		return $this->kClient->batch->getExclusiveJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, $this->getMaxJobsEachRun(), $this->getFilter(), $this->getJobType());
	}
	
	public function run($jobs = null)
	{
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		if(is_null($jobs))
		{
			try
			{
				$jobs = $this->getJobs();
			}
			catch (Exception $e)
			{
				KalturaLog::err($e->getMessage());
				return null;
			}
		}
		
		KalturaLog::info(count($jobs) . " jobs to handle");
		
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
			catch(kApplicativeException $kaex)
			{
				$this->unimpersonate();
				$job = $this->closeJob($job, KalturaBatchJobErrorTypes::APP, $kaex->getCode(), $kaex->getMessage(), KalturaBatchJobStatus::FAILED);
			}
			catch(kTemporaryException $ktex)
			{
				$this->unimpersonate();
				if($ktex->getResetJobExecutionAttempts())
				{
					$this->kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
				}
				$job = $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ktex->getCode(), "Error: " . $ktex->getMessage(), KalturaBatchJobStatus::RETRY);
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
	 * @param int $jobId
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob
	 */
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{
		return $this->kClient->batch->updateExclusiveJob($jobId, $this->getExclusiveLockKey(), $job);
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob
	 */
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if ($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
		
		$response = $this->kClient->batch->freeExclusiveJob($job->id, $this->getExclusiveLockKey(), $this->getJobType(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue($this->getJobType(), $response->queueSize);
		
		return $response->job;
	}
	
	/**
	 * @return KalturaBatchJobFilter
	 */
	protected function getFilter()
	{
		$filter = new KalturaBatchJobFilter();
		if($this->taskConfig->filter)
			$filter = $this->taskConfig->filter;
		
		if ($this->taskConfig->minCreatedAtMinutes && is_numeric($this->taskConfig->minCreatedAtMinutes))
		{
			$minCreatedAt = time() - ($this->taskConfig->minCreatedAtMinutes * 60);
			$filter->createdAtLessThanOrEqual = $minCreatedAt;
		}
		
		return $filter;
	}
	
	/**
	 * @return KalturaExclusiveLockKey
	 */
	protected function getExclusiveLockKey()
	{
		$lockKey = new KalturaExclusiveLockKey();
		$lockKey->schedulerId = $this->getSchedulerId();
		$lockKey->workerId = $this->getId();
		$lockKey->batchIndex = $this->getIndex();
		
		return $lockKey;
	}
	
	/**
	 * @param KalturaBatchJob $job
	 */
	protected function onFree(KalturaBatchJob $job)
	{
		$this->onJobEvent($job, KBatchEvent::EVENT_JOB_FREE);
	}
	
	/**
	 * @param KalturaBatchJob $job
	 */
	protected function onUpdate(KalturaBatchJob $job)
	{
		$this->onJobEvent($job, KBatchEvent::EVENT_JOB_UPDATE);
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @param int $event_id
	 */
	protected function onJobEvent(KalturaBatchJob $job, $event_id)
	{
		$event = new KBatchEvent();
		
		$event->partner_id = $job->partnerId;
		$event->entry_id = $job->entryId;
		$event->bulk_upload_id = $job->bulkJobId;
		$event->batch_parant_id = $job->parentJobId;
		$event->batch_root_id = $job->rootJobId;
		$event->batch_status = $job->status;
		
		$this->onEvent($event_id, $event);
	}
	
	/**
	 * @param string $jobType
	 * @return KalturaWorkerQueueFilter
	 */
	protected function getBaseQueueFilter($jobType)
	{
		$filter = $this->getFilter();
		$filter->jobTypeEqual = $jobType;
		
		$workerQueueFilter = new KalturaWorkerQueueFilter();
		$workerQueueFilter->schedulerId = $this->getSchedulerId();
		$workerQueueFilter->workerId = $this->getId();
		$workerQueueFilter->filter = $filter;
		$workerQueueFilter->jobType = $jobType;
		
		return $workerQueueFilter;
	}
	
	/**
	 * @param string $jobType
	 * @param boolean $isCloser
	 * @return KalturaWorkerQueueFilter
	 */
	protected function getQueueFilter($jobType)
	{
		$workerQueueFilter = $this->getBaseQueueFilter($jobType);
		//$workerQueueFilter->filter->statusIn = KalturaBatchJobStatus::PENDING . ',' . KalturaBatchJobStatus::RETRY;
		
		return $workerQueueFilter;
	}
	
	/**
	 * @param int $jobType
	 */
	protected function saveQueueFilter($jobType)
	{
		$filter = $this->getQueueFilter($jobType);
		
		$type = $this->taskConfig->name;
		$file = "$type.flt";
		KalturaLog::debug("Saving filter to $file: " . print_r($filter, true));
		
		KScheduleHelperManager::saveFilter($file, $filter);
	}
	
	/**
	 * @param int $jobType
	 * @param int $size
	 */
	protected function saveSchedulerQueue($jobType, $size = null)
	{
		if(is_null($size))
		{
			$workerQueueFilter = $this->getQueueFilter($jobType);
			$size = $this->kClient->batch->getQueueSize($workerQueueFilter);
		}
		
		$queueStatus = new KalturaBatchQueuesStatus();
		$queueStatus->workerId = $this->getId();
		$queueStatus->jobType = $jobType;
		$queueStatus->size = $size;
		
		$this->saveSchedulerCommands(array($queueStatus));
	}
	
	/**
	 * @return KalturaBatchJob
	 */
	protected function newEmptyJob()
	{
		return new KalturaBatchJob();
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @param string $msg
	 * @param int $status
	 * @param unknown_type $data
	 * @param boolean $remote
	 * @return KalturaBatchJob
	 */
	protected function updateJob(KalturaBatchJob $job, $msg, $status, KalturaJobData $data = null)
	{
		$updateJob = $this->newEmptyJob();
		
		if(! is_null($msg))
		{
			$updateJob->message = $msg;
			$updateJob->description = $job->description . "\n$msg";
		}
		
		$updateJob->status = $status;
		$updateJob->data = $data;
		
		KalturaLog::info("job[$job->id] status: [$status] msg : [$msg]");
		if($this->isUnitTest)
			return $job;
		
		$job = $this->updateExclusiveJob($job->id, $updateJob);
		if($job instanceof KalturaBatchJob)
			$this->onUpdate($job);
		
		return $job;
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @param int $errType
	 * @param int $errNumber
	 * @param string $msg
	 * @param int $status
	 * @param KalturaJobData $data
	 * @return KalturaBatchJob
	 */
	protected function closeJob(KalturaBatchJob $job, $errType, $errNumber, $msg, $status, $data = null)
	{
		if(! is_null($errType))
			KalturaLog::err($msg);
		
		$updateJob = $this->newEmptyJob();
		
		if(! is_null($msg))
		{
			$updateJob->message = $msg;
			$updateJob->description = $job->description . "\n$msg";
		}
		
		$updateJob->status = $status;
		$updateJob->errType = $errType;
		$updateJob->errNumber = $errNumber;
		$updateJob->data = $data;
		
		KalturaLog::info("job[$job->id] status: [$status] msg : [$msg]");
		if($this->isUnitTest)
		{
			$job->status = $updateJob->status;
			$job->message = $updateJob->message;
			$job->description = $updateJob->description;
			$job->errType = $updateJob->errType;
			$job->errNumber = $updateJob->errNumber;
			return $job;
		}
		
		$job = $this->updateExclusiveJob($job->id, $updateJob);
		if($job instanceof KalturaBatchJob)
			$this->onUpdate($job);
		
		KalturaLog::info("Free job[$job->id]");
		$job = $this->freeExclusiveJob($job);
		if($job instanceof KalturaBatchJob)
			$this->onFree($job);
		
		return $job;		
	}
}
