<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */
require_once("bootstrap.php");

/**
 * Will close almost done conversions that sent to remote systems and store the files in the file system.
 * The state machine of the job is as follows:
 * 	 	get almost done conversions 
 * 		check the convert status
 * 		download the converted file
 * 		save recovery file in case of crash
 * 		move the file to the archive
 *
 * @package Scheduler
 * @subpackage Conversion
 */
class KAsyncConvertCloser extends KBatchBase
{
	private $localTempPath;
	private $sharedTempPath;
	
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CONVERT;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType(), true);
	}
	
	public function run($jobs = null)
	{
		KalturaLog::info("Convert closer is running");

		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		// creates a temp file path
		$this->localTempPath = $this->taskConfig->params->localTempPath;
		$this->sharedTempPath = $this->taskConfig->params->sharedTempPath;
	
		$res = self::createDir( $this->localTempPath );
		if ( !$res ) 
		{
			KalturaLog::err( "Cannot continue conversion without temp local directory");
			return null;
		}
		
		$res = self::createDir( $this->sharedTempPath );
		if ( !$res ) 
		{
			KalturaLog::err( "Cannot continue conversion without temp shared directory");
			return null;
		}
		
		if(is_null($jobs))
		{		
			$jobs = $this->kClient->batch->getExclusiveAlmostDoneConvertJobs(
				$this->getExclusiveLockKey() , 
				$this->taskConfig->maximumExecutionTime , 
				$this->taskConfig->maxJobsEachRun , 
				$this->getFilter());
		}
			
		KalturaLog::info(count($jobs) . " convert jobs to close");
		
		if(! count($jobs))
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType(), null, true);
			return null;
		}
		
		foreach($jobs as &$job)
		{
			try
			{
				$job = $this->closeConvert($job, $job->data);
			}
			catch(KalturaException $kex)
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_API, $kex->getCode(), "Error: " . $kex->getMessage(), KalturaBatchJobStatus::FAILED);
			}
			catch(KalturaClientException $kcex)
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_CLIENT, $kcex->getCode(), "Error: " . $kcex->getMessage(), KalturaBatchJobStatus::RETRY);
			}
			catch(Exception $ex)
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ex->getCode(), "Error: " . $ex->getMessage(), KalturaBatchJobStatus::FAILED);
			}
		}
			
		return $jobs;
	}
	
	private function closeConvert(KalturaBatchJob $job, KalturaConvertJobData $data)
	{
		KalturaLog::debug("fetchStatus($job->id)");
		
		if(($job->queueTime + $this->taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
		
		if($job->jobSubType == KalturaConversionEngineType::ENCODING_COM)
		{
			$parseEngine = new KParseEngineEncodingCom($this->taskConfig);
			$errMessage = null;
			$status = $parseEngine->parse($data, $errMessage);
					
			if($errMessage == $job->message)
				$errMessage = null;
				
			$log = $parseEngine->getLogData();
			if($log && strlen($log))
				$this->kClient->batch->logConversion($data->flavorAssetId, $log);
				
			if($status == KalturaBatchJobStatus::FINISHED)
			{
				$updateData = new KalturaConvertJobData();
				$updateData->destFileSyncRemoteUrl = $data->destFileSyncRemoteUrl;
				$this->updateJob($job, $errMessage, KalturaBatchJobStatus::ALMOST_DONE, 90, $updateData);
			}
			else
			{
				return $this->closeJob($job, null, null, $errMessage, $status);
			}
		}
	
		if($job->jobSubType == KalturaConversionEngineType::KALTURA_COM)
		{
			// TODO 
			// fetch status from kaltura.com
			// if status is not ready - return
			// $data->destFileSyncRemoteUrl = "http://kaltura.com/...";
		}
		
		if($job->executionAttempts > 1) // is a retry
		{
			if(strlen($data->destFileSyncLocalPath) && file_exists($data->destFileSyncLocalPath))
			{
				return $this->moveFile($job, $data);
			}
		}
		
		// creates a temp file path
		$uniqid = uniqid('convert_');
		$data->destFileSyncLocalPath = "{$this->localTempPath}/$uniqid";
	
		$err = null;
		if(!$this->fetchFile($data->destFileSyncRemoteUrl, $data->destFileSyncLocalPath, $err))
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::REMOTE_DOWNLOAD_FAILED, $err, KalturaBatchJobStatus::ALMOST_DONE);
		}
		$this->fetchFile($data->destFileSyncRemoteUrl . '.log', $data->destFileSyncLocalPath . '.log');
		
		return $this->moveFile($job, $data);
	}
	
	private function moveFile(KalturaBatchJob $job, KalturaConvertJobData $data)
	{
		KalturaLog::debug("moveFile($job->id, $data->destFileSyncLocalPath)");
		
		$uniqid = uniqid('convert_');
		$sharedFile = "{$this->sharedTempPath}/$uniqid";
		
		try
		{
			rename($data->destFileSyncLocalPath . '.log', $sharedFile . '.log');
		}
		catch(Exception $ex)
		{
			KalturaLog::debug("move log file error: " . $ex->getMessage());
		}
		
		clearstatcache();
		$fileSize = filesize($data->destFileSyncLocalPath);
		rename($data->destFileSyncLocalPath, $sharedFile);
		if(!file_exists($sharedFile) || filesize($sharedFile) != $fileSize)
		{
			KalturaLog::err("Error: moving file failed");
			die();
		}
		
		@chmod($sharedFile, 0777);
		$data->destFileSyncLocalPath = $sharedFile;
		
		if($this->checkFileExists($sharedFile, $fileSize))
		{
			$job->status = KalturaBatchJobStatus::FINISHED;
			$job->message = "File moved to shared";
		}
		else
		{
			$job->status = KalturaBatchJobStatus::ALMOST_DONE; // retry
			$job->message = "File not moved correctly";
		}
		$updateData = new KalturaConvertJobData();
		$updateData->destFileSyncLocalPath = $data->destFileSyncLocalPath;
		return $this->closeJob($job, null, null, $job->message, $job->status, $updateData);
	}
	
	/**
	 * @param string $srcFileSyncRemoteUrl
	 * @param string $srcFileSyncLocalPath
	 * @param string $errDescription
	 * @return string
	 */
	private function fetchFile($srcFileSyncRemoteUrl, $srcFileSyncLocalPath, &$errDescription = null)
	{
		KalturaLog::debug("fetchFile($srcFileSyncRemoteUrl, $srcFileSyncLocalPath)");
		
		try
		{
			$curlWrapper = new KCurlWrapper($srcFileSyncRemoteUrl);
			$curlHeaderResponse = $curlWrapper->getHeader(true);
			if(!$curlHeaderResponse || $curlWrapper->getError())
			{
				$errDescription = "Error: " . $curlWrapper->getError();
				return false;
			}
			
			if($curlHeaderResponse->code != KCurlHeaderResponse::HTTP_STATUS_OK)
			{
				$errDescription = "HTTP Error: " . $curlHeaderResponse->code . " " . $curlHeaderResponse->codeName;
				return false;
			}
			$fileSize = null;
			if(isset($curlHeaderResponse->headers['content-length']))
				$fileSize = $curlHeaderResponse->headers['content-length'];
			$curlWrapper->close();
				
			KalturaLog::debug("Executing curl");
			$curlWrapper = new KCurlWrapper($srcFileSyncRemoteUrl);
			$res = $curlWrapper->exec($srcFileSyncLocalPath);
			KalturaLog::debug("Curl results: $res");
		
			if(!$res || $curlWrapper->getError())
			{
				$errDescription = "Error: " . $curlWrapper->getError();
				$curlWrapper->close();
				return false;
			}
			$curlWrapper->close();
			
			if(!file_exists($srcFileSyncLocalPath))
			{
				$errDescription = "Error: output file doesn't exist";
				return false;
			}
				
			if($fileSize)
			{
				clearstatcache();
				if(filesize($srcFileSyncLocalPath) != $fileSize)
				{
					$errDescription = "Error: output file have a wrong size";
					return false;
				}
			}
		}
		catch(Exception $ex)
		{
			$errDescription = "Error: " . $ex->getMessage();
			return false;
		}
		
		return true;
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{
		return $this->kClient->batch->updateExclusiveConvertJob($jobId, $this->getExclusiveLockKey(), $job);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
	
		$response = $this->kClient->batch->freeExclusiveConvertJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
}
?>