<?php
require_once('bootstrap.php');

/**
 *
 *
 * @package plugins.multiCenters
 * @subpackage Scheduler.FileSyncImport
 */
class KAsyncFileSyncImportCloser extends KJobCloserWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::FILESYNC_IMPORT;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		if(($job->queueTime + $this->taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
		
		return $this->moveFile($job, $job->data->tmpFilePath, $job->data->destFilePath);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getMaxJobsEachRun()
	 */
	protected function getMaxJobsEachRun()
	{
		return 1;
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
}
