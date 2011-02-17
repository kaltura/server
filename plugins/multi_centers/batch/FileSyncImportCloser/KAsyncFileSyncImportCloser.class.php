<?php
require_once('bootstrap.php');

/**
 *
 *
 * @package plugins.multiCenters
 * @subpackage Scheduler.FileSyncImport
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

		if($this->taskConfig->isInitOnly()) {
			return $this->init();
		}

		if(is_null($jobs)) {
			$jobs = $this->kClient->fileSyncImportBatch->getExclusiveAlmostDoneFileSyncImportJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter());
		}
			
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
				// move file from temp location to final destination
				$job = $this->moveFile($job, $job->data->tmpFilePath, $job->data->destFilePath);
			}
		}
		return $jobs;
	}

	
	private function moveFile(KalturaBatchJob $job, $fromPath, $toPath)
	{
		KalturaLog::debug("moveFile from[$fromPath] to[$toPath]");
		
		// move file/dir to the new location
		$res = @rename($fromPath, $toPath);
		
		// chmod + chown + check file seen by apache - for each moved file/directory
		if ($res)
		{
			if (is_dir($toPath))
			{
				$contents = kFile::listDir($toPath);
				sort($contents, SORT_STRING);
				foreach ($contents as $current)
				{
					$res = $res && $this->setAndCheck($toPath.'/'.$current);				
				}
			}
			else
			{
				$res = $this->setAndCheck($toPath);	
			}
		}

		if($res)
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
	
	
	private function setAndCheck($path)
	{
		// change path owner
		$chown_name = $this->taskConfig->params->fileOwner;
		if ($chown_name) {
			KalturaLog::debug("Changing owner of file [$path] to [$chown_name]");
			@chown($path, $chown_name);
		}
		
		// change path mode
		$chmod_perm = octdec($this->taskConfig->params->fileChmod);
		if (!$chmod_perm) {
			$chmod_perm = 0644;
		}
		KalturaLog::debug("Changing mode of file [$path] to [$chmod_perm]");
		@chmod($path, $chmod_perm);
		
		if (is_dir($path)) {
			return true;
		}
		else {
			// check that file is seen by apache
			return $this->checkFileExists($path);
		}		
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null)
	{
		return $this->kClient->fileSyncImportBatch->updateExclusiveFileSyncImportJob($jobId, $this->getExclusiveLockKey(), $job);
	}

	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if ($job->status == KalturaBatchJobStatus::ALMOST_DONE) {
			$resetExecutionAttempts = true;
		}

		$response = $this->kClient->fileSyncImportBatch->freeExclusiveFileSyncImportJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);

		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);

		return $response->job;
	}

}

