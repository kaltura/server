<?php
require_once('bootstrap.php');

/**
 *
 *
 * @package Scheduler
 * @subpackage FileSyncImport
 */
class KAsyncFileSyncImport extends KBatchBase
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
		KalturaLog::info("FileSyncImport batch is running");

		if($this->taskConfig->isInitOnly())
		return $this->init();

		if(is_null($jobs))
		$jobs = $this->kClient->filesyncImportBatch->getExclusiveFileSyncImportJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter());

		KalturaLog::info(count($jobs) . " filesync import jobs to perform");

		if(!count($jobs) > 0)
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return null;
		}

		$useCloser = $this->taskConfig->params->useCloser;

		foreach($jobs as &$job) {
			
			if ($useCloser) {
				// set tmp file path
				if (!$job->data->tmpFilePath) {
					$job->data->tmpFilePath = $this->getTmpPath($job->data->sourceUrl);
					$this->updateJob($job, "Temp destination set", KalturaBatchJobStatus::PROCESSING, 2, $job->data);
				}
				$fileDestination = $job->data->tmpFilePath;
			}
			else {
				$fileDestination = $job->data->destFilePath;
			}
			
			$job = $this->fetchFile($job, $job->data->sourceUrl, $fileDestination);
		}
			
		return $jobs;
	}

	
	private function getTmpPath($sourceUrl)
	{
		// creates a temp file path 
		$rootPath = $this->taskConfig->params->localTempPath;
		
		$res = self::createDir( $rootPath );
		if ( !$res ) 
		{
			KalturaLog::err( "Cannot continue filesync import without temp directory");
			die(); 
		}
		
		$uniqid = uniqid('filesync_import_');
		$destFile = realpath($rootPath) . "/$uniqid";
		KalturaLog::debug("destFile [$destFile]");
		
		$ext = pathinfo($sourceUrl, PATHINFO_EXTENSION);
		$extArr = explode('?', $ext); // remove query string
		$ext = reset($extArr);
		if(strlen($ext))
			$destFile .= ".$ext";
			
		return $destFile;
	}
	

	private function fetchFile(KalturaBatchJob $job, $sourceUrl, $fileDestination)
	{
		KalturaLog::debug("fetchFile($job->id)");

		try
		{
			KalturaLog::debug("sourceUrl [$sourceUrl], fileDestination[$fileDestination]");
				
			$this->updateJob($job, 'Downloading file header', KalturaBatchJobStatus::QUEUED, 1);
				
			// fetches the http headers
			$curlWrapper = new KCurlWrapper($sourceUrl);
			$curlHeaderResponse = $curlWrapper->getHeader();
			if(!$curlHeaderResponse || $curlWrapper->getError())
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $curlWrapper->getErrorNumber(), "Error: " . $curlWrapper->getError(), KalturaBatchJobStatus::FAILED);
				return $job;
			}
				
			if(!$curlHeaderResponse->isGoodCode())
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::HTTP, $curlHeaderResponse->code, "HTTP Error: " . $curlHeaderResponse->code . " " . $curlHeaderResponse->codeName, KalturaBatchJobStatus::FAILED);
				return $job;
			}
				
			$fileSize = null;
			if (isset($curlHeaderResponse->headers['content-length'])) {
				$fileSize = $curlHeaderResponse->headers['content-length'];
			}
			$curlWrapper->close();


			// check if we can start from specific offset on exising partial content
			$resumeOffset = 0;
			if($fileSize && $fileDestination && file_exists($fileDestination))
			{
				clearstatcache();
				$actualFileSize = filesize($fileDestination);
				if($actualFileSize >= $fileSize)
				{
					$job = $this->checkFile($job, $fileDestination, $fileSize);
					return $job;
				}
				else
				{
					$resumeOffset = $actualFileSize;
				}
			}
				
				
			// get http body
			$curlWrapper = new KCurlWrapper($sourceUrl);
			$curlWrapper->setTimeout($this->taskConfig->params->curlTimeout);

			if($resumeOffset)
			{
				$curlWrapper->setResumeOffset($resumeOffset);
			}
			else
			{
				$res = self::createDir(dirname($fileDestination));
				if ( !$res )
				{
					KalturaLog::err( "Cannot continue filesync import without destination directory");
					die();
				}

				$this->updateJob($job, "Downloading file, size: $fileSize", KalturaBatchJobStatus::PROCESSING, 2);
			}
				
			KalturaLog::debug("Executing curl");
			$res = $curlWrapper->exec($fileDestination);
			KalturaLog::debug("Curl results: $res");

			if (!$res || $curlWrapper->getError())
			{
				$errNumber = $curlWrapper->getErrorNumber();
				if($errNumber != CURLE_OPERATION_TIMEOUTED)
				{
					$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "Error: " . $curlWrapper->getError(), KalturaBatchJobStatus::RETRY);
					$curlWrapper->close();
					return $job;
				}
				else
				{
					clearstatcache();
					$actualFileSize = filesize($fileDestination);
					if($actualFileSize == $resumeOffset)
					{
						$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "Error: " . $curlWrapper->getError(), KalturaBatchJobStatus::RETRY);
						$curlWrapper->close();
						return $job;
					}
				}
			}
			
			$curlWrapper->close();
				
			if(!file_exists($fileDestination))
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_DOESNT_EXIST, "Error: output file doesn't exist", KalturaBatchJobStatus::RETRY);
				return $job;
			}


			if($fileSize)
			{
				clearstatcache();
				$actualFileSize = filesize($fileDestination);
				if ($actualFileSize < $fileSize)
				{
					$percent = floor($actualFileSize * 100 / $fileSize);
					$this->updateJob($job, "Downloaded size: $actualFileSize($percent%)", KalturaBatchJobStatus::PROCESSING, $percent);
					$this->kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
					return $job;
				}
			}
						
			$this->updateJob($job, 'File downloaded', KalturaBatchJobStatus::PROCESSED, 90);
				
			$job = $this->checkFile($job, $fileDestination, $fileSize);
		}
		catch(Exception $ex)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		
		return $job;
	}

	
	/**
	 * @param KalturaBatchJob $job
	 * @param string $destFile
	 * @param int $fileSize
	 * @return KalturaBatchJob
	 */
	private function checkFile(KalturaBatchJob $job, $destFile, $fileSize = null)
	{
		KalturaLog::debug("checkFile($job->id, $destFile, $fileSize)");

		try
		{
			if(!file_exists($destFile))
			{
				KalturaLog::err("Error: file [$destFile] doesn't exist");
				$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: file [$destFile] doesn't exist", KalturaBatchJobStatus::FAILED);
				return $job;
			}

			clearstatcache();
			if($fileSize)
			{
				if(filesize($destFile) != $fileSize)
				{
					KalturaLog::err("Error: file [$destFile] has a wrong size");
					$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: file [$destFile] has a wrong size", KalturaBatchJobStatus::FAILED);
					return $job;
				}
			}
			else
			{
				$fileSize = filesize($destFile);
			}
				
			
			$chown_name = $this->taskConfig->params->fileOwner;
			if ($chown_name) {
				KalturaLog::debug("Changing owner of file [$destFile] to [$chown_name]");
				@chown($destFile, $chown_name);
			}
			
			$chmod_perm = octdec($this->taskConfig->params->fileChmod);
			if (!$chmod_perm) {
				$chmod_perm = 0644;
			}
			KalturaLog::debug("Changing mode of file [$destFile] to [$chmod_perm]");
			@chmod($destFile, $chmod_perm);
				
				
			// IMPORTANT - check's if file is seen by apache
			if(!$this->checkFileExists($destFile, $fileSize))
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'File not moved correctly', KalturaBatchJobStatus::RETRY);
			}
			else {
				if ($this->taskConfig->params->useCloser) {
					// close and mark job as almost done
					$this->closeJob($job, null, null, "File downloaded successfully to tmp space", KalturaBatchJobStatus::ALMOST_DONE, null, $job->data);
				}
				else {
					// close and mark job as finished
					$this->closeJob($job, null, null, "File is in final destination", KalturaBatchJobStatus::FINISHED, null, $job->data);
				}
			}
		}
		catch(Exception $ex)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		
		return $job;
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

