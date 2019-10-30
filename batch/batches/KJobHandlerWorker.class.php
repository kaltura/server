<?php
/**
 * Base class for all job handler workers.
 *
 * @package Scheduler
 */
abstract class KJobHandlerWorker extends KBatchBase
{
	/**
	 * The job object that currently handled
	 * @var KalturaBatchJob
	 */
	private static $currentJob;

	/**
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob
	 */
	abstract protected function exec(KalturaBatchJob $job);

	/**
	 * Returns the job object that currently handled
	 * @return KalturaBatchJob
	 */
	public static function getCurrentJob()
	{
		return self::$currentJob;
	}

	/**
	 * @param KalturaBatchJob $currentJob
	 */
	protected static function setCurrentJob(KalturaBatchJob $currentJob)
	{
		KalturaLog::debug("Start job[$currentJob->id] type[$currentJob->jobType] sub-type[$currentJob->jobSubType] object[$currentJob->jobObjectType] object-id[$currentJob->jobObjectId] partner-id[$currentJob->partnerId] dc[$currentJob->dc] parent-id[$currentJob->parentJobId] root-id[$currentJob->rootJobId]");
		self::$currentJob = $currentJob;

		self::$kClient->setClientTag(self::$clientTag . " partnerId: " . $currentJob->partnerId);
	}

	protected static function unsetCurrentJob()
	{
		$currentJob = self::getCurrentJob();
		KalturaLog::debug("End job[$currentJob->id]");
		self::$currentJob = null;

		self::$kClient->setClientTag(self::$clientTag);
	}

	protected function init()
	{
		$this->saveQueueFilter(static::getType());
	}

	protected function getMaxJobsEachRun()
	{
		if(!KBatchBase::$taskConfig->maxJobsEachRun)
			return 1;

		return KBatchBase::$taskConfig->maxJobsEachRun;
	}

	protected function getJobs()
	{
		$maxJobToPull = KBatchBase::$taskConfig->maxJobToPullToCache;
		return KBatchBase::$kClient->batch->getExclusiveJobs($this->getExclusiveLockKey(), KBatchBase::$taskConfig->maximumExecutionTime,
				$this->getMaxJobsEachRun(), $this->getFilter(), static::getType(), $maxJobToPull);
	}

	public function run($jobs = null)
	{
		if(KBatchBase::$taskConfig->isInitOnly())
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
			$this->saveSchedulerQueue(static::getType(), 0);
			return null;
		}

		foreach($jobs as &$job)
		{
			try
			{
				self::setCurrentJob($job);
				$this->validateFileAccess($job);
				$job = $this->exec($job);
				self::unimpersonate();
			}
			catch(KalturaException $kex)
			{
				self::unimpersonate();
				$this->closeJobOnError($job,KalturaBatchJobErrorTypes::KALTURA_API, $kex, KalturaBatchJobStatus::FAILED);
			}
			catch(kApplicativeException $kaex)
			{
				self::unimpersonate();
				$this->closeJobOnError($job,KalturaBatchJobErrorTypes::APP, $kaex, KalturaBatchJobStatus::FAILED);
			}
			catch(kTemporaryException $ktex)
			{
				self::unimpersonate();
				if($ktex->getResetJobExecutionAttempts())
					KBatchBase::$kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);

				$this->closeJobOnError($job,KalturaBatchJobErrorTypes::RUNTIME, $ktex, KalturaBatchJobStatus::RETRY, $ktex->getData());
			}
			catch(KalturaClientException $kcex)
			{
				self::unimpersonate();
				$this->closeJobOnError($job,KalturaBatchJobErrorTypes::KALTURA_CLIENT, $kcex, KalturaBatchJobStatus::RETRY);
			}
			catch(Exception $ex)
			{
				self::unimpersonate();
				$this->closeJobOnError($job,KalturaBatchJobErrorTypes::RUNTIME, $ex, KalturaBatchJobStatus::FAILED);
			}
			self::unsetCurrentJob();
		}

		return $jobs;
	}

	protected function closeJobOnError($job, $error, $ex, $status, $data = null)
	{
		try
		{
			self::unimpersonate();
			$job = $this->closeJob($job, $error, $ex->getCode(), "Error: " . $ex->getMessage(), $status, $data);
		}
		catch(Exception $ex)
		{
			KalturaLog::err("Failed to close job after expirencing an error.");
			KalturaLog::err($ex->getMessage());
		}
	}

	/**
	 * @param int $jobId
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob
	 */
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{
		return KBatchBase::$kClient->batch->updateExclusiveJob($jobId, $this->getExclusiveLockKey(), $job);
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

		$response = KBatchBase::$kClient->batch->freeExclusiveJob($job->id, $this->getExclusiveLockKey(), static::getType(), $resetExecutionAttempts);

		if(is_numeric($response->queueSize)) {
			KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
			$this->saveSchedulerQueue(static::getType(), $response->queueSize);
		}

		return $response->job;
	}

	/**
	 * @return KalturaBatchJobFilter
	 */
	protected function getFilter()
	{
		$filter = new KalturaBatchJobFilter();
		if(KBatchBase::$taskConfig->filter)
			$filter = KBatchBase::$taskConfig->filter;

		if (KBatchBase::$taskConfig->minCreatedAtMinutes && is_numeric(KBatchBase::$taskConfig->minCreatedAtMinutes))
		{
			$minCreatedAt = time() - (KBatchBase::$taskConfig->minCreatedAtMinutes * 60);
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

		$type = KBatchBase::$taskConfig->name;
		$file = "$type.flt";

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
			$size = KBatchBase::$kClient->batch->getQueueSize($workerQueueFilter);
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
