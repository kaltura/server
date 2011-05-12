<?php
require_once("bootstrap.php");
/**
 * Will import a single metadata XML and store it in the file system
 * The state machine of the job is as follows:
 * 	 	parse URL	(youTube is a special case) 
 * 		fetch heraders (to calculate the size of the file)
 * 		fetch file (update the job's progress - 100% is when the whole file as appeared in the header)
 * 		move the file to the archive
 * 		set the entry's new status and file details  (check if FLV) 
 *
 * @package plugins.metadata
 * @subpackage Scheduler.Import
 */
class KAsyncImportMetadata extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::METADATA_IMPORT;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	public function run($jobs = null)
	{
		KalturaLog::info("Import metadata batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		if(is_null($jobs))
			$jobs = $this->kClient->metadataBatch->getExclusiveImportMetadataJobsAction($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, $this->taskConfig->maxJobsEachRun, $this->getFilter());
		
		KalturaLog::info(count($jobs) . " import jobs to perform");
		
		if(! count($jobs) > 0)
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return null;
		}
		
		foreach($jobs as &$job)
			$job = $this->fetchFile($job, $job->data);
			
		return $jobs;
	}
	
	/*
	 * Will take a single KalturaBatchJob and fetch the URL to the job's destFile 
	 */
	private function fetchFile(KalturaBatchJob $job, KalturaImportMetadataJobData $data)
	{
		KalturaLog::debug("fetchFile($job->id)");
		
		try
		{
			$sourceUrl = $data->srcFileUrl;
			KalturaLog::debug("sourceUrl [$sourceUrl]");
			
			$this->updateJob($job, 'Downloading file header', KalturaBatchJobStatus::QUEUED, 1);
			
			$curlWrapper = new KCurlWrapper($sourceUrl);
			$curlHeaderResponse = $curlWrapper->getHeader();
			if(!$curlHeaderResponse || $curlWrapper->getError())
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $curlWrapper->getErrorNumber(), "Error: " . $curlWrapper->getError(), KalturaBatchJobStatus::FAILED, KalturaEntryStatus::ERROR_IMPORTING);
				return $job;
			}
			
			if(!$curlHeaderResponse->isGoodCode())
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::HTTP, $curlHeaderResponse->code, "HTTP Error: " . $curlHeaderResponse->code . " " . $curlHeaderResponse->codeName, KalturaBatchJobStatus::FAILED, KalturaEntryStatus::ERROR_IMPORTING);
				return $job;
			}
			$fileSize = null;
			if(isset($curlHeaderResponse->headers['content-length']))
				$fileSize = $curlHeaderResponse->headers['content-length'];
			$curlWrapper->close();
				
			$resumeOffset = 0;
				
			if($fileSize && $data->destFileLocalPath && file_exists($data->destFileLocalPath))
			{
				clearstatcache();
				$actualFileSize = filesize($data->destFileLocalPath);
				if($actualFileSize >= $fileSize)
				{
					return $this->moveFile($job, $data->destFileLocalPath, $fileSize);
				}
				else
				{
					$resumeOffset = $actualFileSize;
				}
			}
			
			$curlWrapper = new KCurlWrapper($sourceUrl);
			$curlWrapper->setTimeout($this->taskConfig->params->curlTimeout);			
				
			if($resumeOffset)
			{
				$curlWrapper->setResumeOffset($resumeOffset);
			}
			else
			{
				// creates a temp file path 
				$rootPath = $this->taskConfig->params->localTempPath;
				
				$res = self::createDir( $rootPath );
				if ( !$res ) 
				{
					KalturaLog::err( "Cannot continue import without temp directory");
					die(); 
				}
				
				$uniqid = uniqid('import_');
				$destFile = realpath($rootPath) . "/$uniqid.xml";
				KalturaLog::debug("destFile [$destFile]");
				
				$data->destFileLocalPath = $destFile;
				$this->updateJob($job, "Downloading file, size: $fileSize", KalturaBatchJobStatus::PROCESSING, 2, $data);
			}
			
			KalturaLog::debug("Executing curl");
			$res = $curlWrapper->exec($data->destFileLocalPath);
			KalturaLog::debug("Curl results: $res");
		
			if(!$res || $curlWrapper->getError())
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
					$actualFileSize = filesize($data->destFileLocalPath);
					if($actualFileSize == $resumeOffset)
					{
						$this->closeJob($job, KalturaBatchJobErrorTypes::CURL, $errNumber, "Error: " . $curlWrapper->getError(), KalturaBatchJobStatus::RETRY);
						$curlWrapper->close();
						return $job;
					}
				}
			}
			$curlWrapper->close();
			
			if(!file_exists($data->destFileLocalPath))
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_DOESNT_EXIST, "Error: output file doesn't exist", KalturaBatchJobStatus::RETRY);
				return $job;
			}
				
			// check the file size only if its first or second retry
			// in case it failed few times, taks the file as is
			if($fileSize)
			{
				clearstatcache();
				$actualFileSize = filesize($data->destFileLocalPath);
				if($actualFileSize < $fileSize)
				{
					$percent = floor($actualFileSize * 100 / $fileSize);
					$this->updateJob($job, "Downloaded size: $actualFileSize($percent%)", KalturaBatchJobStatus::PROCESSING, $percent, $data);
					$this->kClient->batch->resetJobExecutionAttempts($job->id, $this->getExclusiveLockKey(), $job->jobType);
//					$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_WRONG_SIZE, "Expected file size[$fileSize] actual file size[$actualFileSize]", KalturaBatchJobStatus::RETRY);
					return $job;
				}
			}
			
			
			$this->updateJob($job, 'File imported, copy to shared folder', KalturaBatchJobStatus::PROCESSED, 90);
			
			$job = $this->moveFile($job, $data->destFileLocalPath, $fileSize);
		}
		catch(Exception $ex)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED, KalturaEntryStatus::ERROR_IMPORTING);
		}
		return $job;
	}
	
	/**
	 * @param KalturaBatchJob $job
	 * @param string $destFile
	 * @param int $fileSize
	 * @return KalturaBatchJob
	 */
	private function moveFile(KalturaBatchJob $job, $destFile, $fileSize = null)
	{
		KalturaLog::debug("moveFile($job->id, $destFile, $fileSize)");
		
		try
		{
			// creates a shared file path 
			$rootPath = $this->taskConfig->params->sharedTempPath;
			
			$res = self::createDir( $rootPath );
			if ( !$res ) 
			{
				KalturaLog::err( "Cannot continue import without shared directory");
				die(); 
			}
			$uniqid = uniqid('import_');
			$sharedFile = realpath($rootPath) . "/$uniqid.xml";
			
			KalturaLog::debug("rename('$destFile', '$sharedFile')");
			rename($destFile, $sharedFile);
			if(!file_exists($sharedFile))
			{
				KalturaLog::err("Error: renamed file doesn't exist");
				die();
			}
				
			clearstatcache();
			if($fileSize)
			{
				if(filesize($sharedFile) != $fileSize)
				{
					KalturaLog::err("Error: renamed file have a wrong size");
					die();
				}
			}
			else
			{
				$fileSize = filesize($sharedFile);
			}
			
			@chmod($sharedFile, 0777);
			
			$data = $job->data;
			$data->destFileLocalPath = $sharedFile;
			
			if($this->checkFileExists($sharedFile, $fileSize))
			{
				$this->closeJob($job, null, null, 'Succesfully moved file', KalturaBatchJobStatus::FINISHED, null, $data);
			}
			else
			{
				$this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'File not moved correctly', KalturaBatchJobStatus::RETRY);
			}
		}
		catch(Exception $ex)
		{
			$this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED, KalturaEntryStatus::ERROR_IMPORTING);
		}
		return $job;
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null)
	{
		return $this->kClient->metadataBatch->updateExclusiveImportMetadataJobAction($jobId, $this->getExclusiveLockKey(), $job, $entryStatus);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$response = $this->kClient->metadataBatch->freeExclusiveImportMetadataJobAction($job->id, $this->getExclusiveLockKey(), false);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
}
?>