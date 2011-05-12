<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */
require_once("bootstrap.php");

/**
 * Will convert a collection of flavors and store it in the file system.
 * The state machine of the job is as follows:
 * 	 	get the flavors 
 * 		convert using the right method
 * 		save recovery file in case of crash
 * 		move the file to the archive
 * 		set the entry's new status and file details 
 *
 * @package Scheduler
 * @subpackage Conversion
 */
class KAsyncConvertCollection extends KAsyncConvert
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CONVERT_COLLECTION;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	public function run($jobs = null)
	{
		KalturaLog::notice ( "Convert collection batch is running");
		
		return parent::run($jobs);
	}
	
	protected function getJobs()
	{
		return $this->kClient->batch->getExclusiveConvertCollectionJobs( 
					$this->getExclusiveLockKey() , 
					$this->taskConfig->maximumExecutionTime , 
					$this->getMaxJobsEachRun() , 
					$this->getFilter());
	}
	
	protected function convertImpl(KalturaBatchJob $job, KalturaConvartableJobData $data)
	{
		return $this->convertCollection($job, $data);
	}
	
	private function filesExist($relativePath, array $files)
	{
		if(!count($files))
			return false;
	
		$filesExist = true;
		foreach($files as $file)
		{
			if(!file_exists($file))
				$filesExist = false;
		}
		return $filesExist;
	}
	
	private function convertCollection(KalturaBatchJob $job, KalturaConvertCollectionJobData $data)
	{
		KalturaLog::debug ( "Converting collection job");
	
		
		if($this->taskConfig->params->isRemote || !strlen(trim($data->actualSrcFileSyncLocalPath))) // for distributed conversion
		{
			if(!strlen(trim($data->actualSrcFileSyncLocalPath)))
				$data->actualSrcFileSyncLocalPath = $this->taskConfig->params->localFileRoot . DIRECTORY_SEPARATOR . basename($data->srcFileSyncRemoteUrl);
				
			$err = null;
			if(!$this->distributedFileManager->getLocalPath($data->actualSrcFileSyncLocalPath, $data->srcFileSyncRemoteUrl, $err))
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::REMOTE_FILE_NOT_FOUND, $err, KalturaBatchJobStatus::RETRY);
			}
		}
		
		if(file_exists($data->actualSrcFileSyncLocalPath))
		{
			KalturaLog::debug("Source file exists [$data->actualSrcFileSyncLocalPath]");
		}
		else
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file not found [$data->actualSrcFileSyncLocalPath]", KalturaBatchJobStatus::RETRY);
		}
		
		if(!is_file($data->actualSrcFileSyncLocalPath))
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file [$data->actualSrcFileSyncLocalPath] is not a file", KalturaBatchJobStatus::FAILED);
		}
		
		$data->destDirLocalPath = $this->localTempPath;
		KalturaLog::debug ( "Destination folder: " . $data->destDirLocalPath);
		
		$data->inputXmlLocalPath = $this->translateSharedPath2Local($data->inputXmlLocalPath);
	
		if($this->taskConfig->params->isRemote || !strlen(trim($data->inputXmlLocalPath))) // for distributed conversion
		{
			if(!strlen(trim($data->inputXmlLocalPath)))
				$data->inputXmlLocalPath = $this->taskConfig->params->localFileRoot . DIRECTORY_SEPARATOR . basename($data->inputXmlLocalPath);
				
			$err = null;
			if(!$this->distributedFileManager->getLocalPath($data->inputXmlLocalPath, $data->inputXmlRemoteUrl, $err))
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::REMOTE_FILE_NOT_FOUND, $err, KalturaBatchJobStatus::RETRY);
			}
		}
		
		if(file_exists($data->inputXmlLocalPath))
		{
			KalturaLog::debug("XML Configuration file exists [$data->inputXmlLocalPath]");
		}
		else
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, null, "XML Configuration file not found [$data->inputXmlLocalPath]", KalturaBatchJobStatus::RETRY);
		}
		
		if(!is_file($data->inputXmlLocalPath))
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "XML Configuration file [$data->inputXmlLocalPath] is not a file", KalturaBatchJobStatus::FAILED);
		}
		
		$logFilePath = $data->destDirLocalPath . DIRECTORY_SEPARATOR . $data->destFileName . '.log';
		$monitorFiles = array(
			$logFilePath
		);
		$this->startMonitor($monitorFiles);
	
		$operator = $this->getOperator($data);
		KalturaLog::debug("getOperator(".print_r($data,true).") => operator(".print_r($operator,true).")");
		$log = null;
		try
		{
			$this->operationEngine->operate($operator, $data->actualSrcFileSyncLocalPath, $data->destFileSyncLocalPath);
		}
		catch(KOperationEngineException $e)
		{
			$log = $this->operationEngine->getLogData();
			
			$err = "engine [" . get_class($this->operationEngine) . "] convert failed: " . $e->getMessage();
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CONVERSION_FAILED, $err, KalturaBatchJobStatus::FAILED);
		}
		$this->stopMonitor();
	
		$videoEntities = $this->operationEngine->getOutFilesPath();
		foreach($videoEntities as $bitrate => $flavorPath)
		{
			foreach($data->flavors as $index => $flavor)
				if($flavor->videoBitrate == $bitrate)
					$data->flavors[$index]->destFileSyncLocalPath = $flavorPath;
		}
		KalturaLog::debug ( "Flavors data: " . print_r($data->flavors, true));
			
		$job = $this->updateJob($job, "engine [" . get_class($this->operationEngine) . "] convert successfully", KalturaBatchJobStatus::MOVEFILE, 90, $data);
		return $this->moveFiles($job, $job->data);
	}
	
	private function moveFiles(KalturaBatchJob $job, KalturaConvertCollectionJobData $data)
	{
		KalturaLog::debug("moveFiles(destDirLocalPath [$data->destDirLocalPath])");
		
		clearstatcache();
		$files2move = array();
		
		$fileNames = array(
			$data->destFileName . '.log',
			$data->destFileName . '.ism',
			$data->destFileName . '.ismc',
			$data->destFileName . '_Thumb.jpg'
		);
		
		foreach($fileNames as $fileName)
		{
			$srcPath = $data->destDirLocalPath . DIRECTORY_SEPARATOR . $fileName;
			if(!file_exists($srcPath))
				continue;
				
			$destPath = $this->sharedTempPath . DIRECTORY_SEPARATOR . $fileName;
			$sharedPath = $this->translateLocalPath2Shared($destPath);
			$fileSize = filesize($srcPath);
			
			KalturaLog::debug("add to move list file[$srcPath] to[$destPath] size[$fileSize] shared path[$sharedPath]");
			$files2move[] = array(
				'from' => $srcPath,
				'to' => $destPath,
				'path' => $sharedPath,
				'size' => $fileSize,
			);
		}
		
		foreach($data->flavors as $flavor)
		{
			$srcPath = $flavor->destFileSyncLocalPath;
			if(!file_exists($srcPath))
				continue;
				
			$destPath = $this->sharedTempPath . DIRECTORY_SEPARATOR . basename($srcPath);
			$sharedPath = $this->translateLocalPath2Shared($destPath);
			$fileSize = filesize($srcPath);
			
			$flavor->destFileSyncLocalPath = $sharedPath;
			if($this->taskConfig->params->isRemote)
				$flavor->destFileSyncRemoteUrl = $this->distributedFileManager->getRemoteUrl($sharedPath);
						
			KalturaLog::debug("add to move list file[$srcPath] to[$destPath] size[$fileSize] shared path[$sharedPath]");
			$files2move[] = array(
				'from' => $srcPath,
				'to' => $destPath,
				'path' => $sharedPath,
				'size' => $fileSize,
			);
		} 
		
		foreach($files2move as $file2move)
		{
			$srcPath = $file2move['from'];
			$destPath = $file2move['to'];
			$fileSize = $file2move['size'];
			
			KalturaLog::debug("moving file[$srcPath] to[$destPath] size[$fileSize]");
			
			if(file_exists($destPath))
			{
				KalturaLog::debug("delete existing file[$destPath]");
				unlink($destPath);
			}
				
			KalturaLog::debug("rename($srcPath, $destPath)");
			rename($srcPath, $destPath);
		
			if(!file_exists($destPath) || filesize($destPath) != $fileSize)
			{
				KalturaLog::err("Error: moving file [$srcPath] failed");
				die();
			}
			@chmod($destPath, 0777);
		}
		
		$data->destDirLocalPath = $this->translateLocalPath2Shared($this->sharedTempPath);
		if($this->taskConfig->params->isRemote) // for remote conversion
		{			
			$data->destDirRemoteUrl = $this->distributedFileManager->getRemoteUrl($data->destDirLocalPath);
			$job->status = KalturaBatchJobStatus::ALMOST_DONE;
			$job->message = "Files ready for download";
		}
		elseif($this->checkFilesArrayExist($files2move))
		{
			$job->status = KalturaBatchJobStatus::FINISHED;
			$job->message = "Files moved to shared";
		}
		else
		{
			$job->status = KalturaBatchJobStatus::RETRY;
			$job->message = "Files not moved correctly";
		}
		return $this->closeJob($job, null, null, $job->message, $job->status, $data);
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{
		$flavors = null;
		if($job->data->flavors)
		{
			$flavors = $job->data->flavors;
			$job->data->flavors = null;
		}
		return $this->kClient->batch->updateExclusiveConvertCollectionJob($jobId, $this->getExclusiveLockKey(), $job, $flavors);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
			
		$response = $this->kClient->batch->freeExclusiveConvertCollectionJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}	

	
	/*
	 * @return string
	 */
	protected function getSupportedEnginesDescription()
	{
		return "[" . KalturaConversionEngineType::EXPRESSION_ENCODER3 . "] Expression Encoder 3";
	}
	
	/*
	 * @return string
	 */
	protected function getSupportedEngines()
	{
		return KalturaConversionEngineType::EXPRESSION_ENCODER3;
	}
}
?>