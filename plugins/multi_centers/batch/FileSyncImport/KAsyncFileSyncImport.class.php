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

		if($this->taskConfig->isInitOnly()) {
			return $this->init();
		}

		if(is_null($jobs)) {
			$jobs = $this->kClient->fileSyncImportBatch->getExclusiveFileSyncImportJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter());
		}
			
		KalturaLog::info(count($jobs) . " filesync import jobs to perform");

		if(!count($jobs) > 0)
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return null;
		}

		$useCloser = $this->taskConfig->params->useCloser;

		foreach($jobs as &$job)
		{
			
			if ($useCloser)
			{
				// if closer is used, the file will be download to a temporary directory, and then moved to its final destination by the KAsyncFileSyncImportCloser batch
				if (!$job->data->tmpFilePath)
				{
					// adding temp path to the job data, so that the closer will be able to use it later
					$job->data->tmpFilePath = $this->getTmpPath($job->data->sourceUrl);
					$this->updateJob($job, "Temp destination set", KalturaBatchJobStatus::PROCESSING, 2, $job->data);
				}
				// destination = temporary path
				$fileDestination = $job->data->tmpFilePath;
			}
			else
			{
				// destination = final path
				$fileDestination = $job->data->destFilePath;
			}
			
			// start downoading the file to its destination (temp or final)
			$job = $this->fetchFile($job, $job->data->sourceUrl, $fileDestination);
		}
			
		return $jobs;
	}

	
	private function getTmpPath($sourceUrl)
	{
		// create a temporary file path 
		$rootPath = $this->taskConfig->params->localTempPath;
		
		$res = self::createDir( $rootPath );
		if ( !$res ) 
		{
			KalturaLog::err( "Cannot continue filesync import without a temp directory");
			die(); 
		}
		
		// add a unique id to the temporary file path
		$uniqid = uniqid('filesync_import_');
		$destFile = realpath($rootPath) . "/$uniqid";
		KalturaLog::debug("destFile [$destFile]");
		
		// add file extension if any
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
				
			// fetch the http headers
			$curlWrapper = new KCurlWrapper($sourceUrl);
			$curlHeaderResponse = $curlWrapper->getHeader();
			if(!$curlHeaderResponse || $curlWrapper->getError())
			{
				// error fetching headers
				$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $curlWrapper->getErrorNumber(), "Error: " . $curlWrapper->getError(), KalturaBatchJobStatus::FAILED);
				return $job;
			}
				
			if(!$curlHeaderResponse->isGoodCode())
			{
				// some error exists in the response
				$this->closeJob($job, KalturaBatchJobErrorTypes::HTTP, $curlHeaderResponse->code, "HTTP Error: " . $curlHeaderResponse->code . " " . $curlHeaderResponse->codeName, KalturaBatchJobStatus::FAILED);
				return $job;
			}
			
			// try to get file size from headers
			$fileSize = null;
			if (isset($curlHeaderResponse->headers['content-length'])) {
				$fileSize = $curlHeaderResponse->headers['content-length'];
			}
			$curlWrapper->close();


			// if file already exists - check if we can start from specific offset on exising partial content
			$resumeOffset = 0;
			if($fileSize && $fileDestination && file_exists($fileDestination))
			{
				clearstatcache();
				$actualFileSize = filesize($fileDestination);
				if($actualFileSize >= $fileSize)
				{
					// file download finished ?
					$job = $this->checkFile($job, $fileDestination, $fileSize);
					return $job;
				}
				else
				{
					// will resume from the current offset
					$resumeOffset = $actualFileSize;
				}
			}
				
				
			// get http body
			$curlWrapper = new KCurlWrapper($sourceUrl);
			$curlWrapper->setTimeout($this->taskConfig->params->curlTimeout);

			if($resumeOffset)
			{
				// will resume from the current offset
				$curlWrapper->setResumeOffset($resumeOffset);
			}
			else
			{
				// create destination directory if doesn't already exist
				$res = self::createDir(dirname($fileDestination));
				if ( !$res )
				{
					KalturaLog::err( "Cannot continue filesync import without destination directory");
					die();
				}
				
				// about to start downloading
				$this->updateJob($job, "Downloading file, size: $fileSize", KalturaBatchJobStatus::PROCESSING, 2);
			}
				
			KalturaLog::debug("Executing curl");
			$res = $curlWrapper->exec($fileDestination); // download file
			KalturaLog::debug("Curl results: $res");

			// handle errors
			if (!$res || $curlWrapper->getError())
			{
				$errNumber = $curlWrapper->getErrorNumber();
				if($errNumber != CURLE_OPERATION_TIMEOUTED)
				{
					// an error other than timeout occured  - cannot continue (timeout is handled with resuming)
					$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "Error: " . $curlWrapper->getError(), KalturaBatchJobStatus::RETRY);
					$curlWrapper->close();
					return $job;
				}
				else
				{
					// timeout error occured
					clearstatcache();
					$actualFileSize = filesize($fileDestination);
					if($actualFileSize == $resumeOffset)
					{
						// no downloading was done at all - error
						$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "Error: " . $curlWrapper->getError(), KalturaBatchJobStatus::RETRY);
						$curlWrapper->close();
						return $job;
					}
				}
			}
			
			$curlWrapper->close();
				
			if(!file_exists($fileDestination))
			{
				// destination file does not exist for an unknown reason
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_DOESNT_EXIST, "Error: output file doesn't exist", KalturaBatchJobStatus::RETRY);
				return $job;
			}


			if($fileSize)
			{
				clearstatcache();
				$actualFileSize = filesize($fileDestination);
				if ($actualFileSize < $fileSize)
				{
					// part of file was downloaded - will resume in next run
					$percent = floor($actualFileSize * 100 / $fileSize);
					$this->updateJob($job, "Downloaded size: $actualFileSize($percent%)", KalturaBatchJobStatus::PROCESSING, $percent);
					$this->kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
					return $job;
				}
			}
						
			$this->updateJob($job, 'File downloaded', KalturaBatchJobStatus::PROCESSED, 90);
			
			// file downloaded completely - check it
			$job = $this->checkFile($job, $fileDestination, $fileSize);
		}
		catch(Exception $ex)
		{
			// run time error occured
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		
		return $job;
	}

	
	/**
	 * Checks downloaded file.
	 * Changes the file mode and owner if required.
	 * 
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
				// destination file does not exist
				KalturaLog::err("Error: file [$destFile] doesn't exist");
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_DOESNT_EXIST, "Error: file [$destFile] doesn't exist", KalturaBatchJobStatus::FAILED);
				return $job;
			}

			clearstatcache();
			if($fileSize)
			{
				if(filesize($destFile) != $fileSize)
				{
					// destination file size is wrong
					KalturaLog::err("Error: file [$destFile] has a wrong size");
					$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_WRONG_SIZE, "Error: file [$destFile] has a wrong size", KalturaBatchJobStatus::FAILED);
					return $job;
				}
			}
			else
			{
				$fileSize = filesize($destFile);
			}
				
			// set file owner
			$chown_name = $this->taskConfig->params->fileOwner;
			if ($chown_name) {
				KalturaLog::debug("Changing owner of file [$destFile] to [$chown_name]");
				@chown($destFile, $chown_name);
			}
			
			// set file mode
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
			// run time error occured
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		
		return $job;
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

