<?php
require_once('bootstrap.php');

/**
 *
 *
 * @package Scheduler
 * @subpackage FileSyncImportCloser
 */
class KAsyncFileSyncImportCloser extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::FILESYNC_IMPORT;
	}

	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}

	
	public function run($jobs = null)
	{
		KalturaLog::info("FileSyncImportCloser batch is running");

		if($this->taskConfig->isInitOnly())
		return $this->init();

		if(is_null($jobs))
		$jobs = $this->kClient->filesyncImportBatch->getExclusiveAlmostDoneFileSyncImportJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter());
		
		KalturaLog::info(count($jobs) . " filesync import closer jobs to perform");

		if(!count($jobs) > 0)
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return null;
		}

		foreach($jobs as &$job) {
			if(($job->queueTime + $this->taskConfig->params->maxTimeBeforeFail) < time()) {
				$job = $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
			}
			else {
				$job = $this->moveFile($job, $job->data->tmpFilePath, $job->data->destFilePath);
			}
		}
		return $jobs;
	}

	
	private function moveFile(KalturaBatchJob $job, $fromPath, $toPath)
	{
		KalturaLog::debug("moveFile from[$fromPath] to[$toPath]");
		
		@rename($fromPath, $toPath);
		
		$chown_name = $this->taskConfig->params->fileOwner;
		if ($chown_name) {
			KalturaLog::debug("Changing owner of file [$toPath] to [$chown_name]");
			@chown($toPath, $chown_name);
		}
		
		$chmod_perm = octdec($this->taskConfig->params->fileChmod);
		if (!$chmod_perm) {
			$chmod_perm = 0644;
		}
		KalturaLog::debug("Changing mode of file [$toPath] to [$chmod_perm]");
		@chmod($toPath, $chmod_perm);
				
		if($this->checkFileExists($toPath))
		{
			$job->status = KalturaBatchJobStatus::FINISHED;
			$job->message = "File moved to final destination";
		}
		else
		{
			$job->status = KalturaBatchJobStatus::FAILED;
			$job->message = "File not moved correctly";
		}
		return $this->closeJob($job, null, null, $job->message, $job->status, null, $job->data);
	}
	
	
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null)
	{
		return $this->kClient->filesyncImportBatch->updateExclusiveFileSyncImportJob($jobId, $this->getExclusiveLockKey(), $job);
	}

	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if ($job->status == KalturaBatchJobStatus::ALMOST_DONE) {
			$resetExecutionAttempts = true;
		}

		$response = $this->kClient->filesyncImportBatch->freeExclusiveFileSyncImportJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);

		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);

		return $response->job;
	}

}

