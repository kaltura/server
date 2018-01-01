<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */

/**
 * Will convert a single flavor and store it in the file system.
 * The state machine of the job is as follows:
 * 	 	get the flavor
 * 		convert using the right method
 * 		save recovery file in case of crash
 * 		move the file to the archive
 * 		set the entry's new status and file details
 *
 * @package Scheduler
 * @subpackage Conversion
 */
class KAsyncConvert extends KJobHandlerWorker
{
	/**
	 * @var string
	 */
	protected $localTempPath;
	
	/**
	 * @var string
	 */
	protected $sharedTempPath;
	
	/**
	 * @var KDistributedFileManager
	 */
	protected $distributedFileManager = null;
	
	/**
	 * @var KOperationEngine
	 */
	protected $operationEngine = null;
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CONVERT;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->convert($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getFilter()
	 */
	protected function getFilter()
	{
		$filter = parent::getFilter();
		
		if(self::$taskConfig->params->minFileSize && is_numeric(self::$taskConfig->params->minFileSize))
			$filter->fileSizeGreaterThan = self::$taskConfig->params->minFileSize;
		
		if(self::$taskConfig->params->maxFileSize && is_numeric(self::$taskConfig->params->maxFileSize))
			$filter->fileSizeLessThan = self::$taskConfig->params->maxFileSize;
			
		return $filter;
	}
		
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::run()
	 */
	public function run($jobs = null)
	{
		// creates a temp file path
		$this->localTempPath = self::$taskConfig->params->localTempPath;
		$this->sharedTempPath = self::$taskConfig->params->sharedTempPath;
	
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
		
		$remoteFileRoot = self::$taskConfig->getRemoteServerUrl() . self::$taskConfig->params->remoteUrlDirectory;
		$this->distributedFileManager = new KDistributedFileManager(self::$taskConfig->params->localFileRoot, $remoteFileRoot, self::$taskConfig->params->fileCacheExpire);
		
		return parent::run($jobs);
	}
	
	protected function convert(KalturaBatchJob $job, KalturaConvartableJobData $data)
	{
			/*
			 * When called for 'collections', the 'flavorParamsOutputId' is not set.
			 * It is set in the 'flavors' array, but for collections the 'flavorParamsOutput' it is unrequired.
			 */
		if(isset($data->flavorParamsOutputId))
			$data->flavorParamsOutput = self::$kClient->flavorParamsOutput->get($data->flavorParamsOutputId);
		
		foreach ($data->srcFileSyncs as $srcFileSyncDescriptor) 
		{
			$srcFileSyncDescriptor->actualFileSyncLocalPath = $this->translateSharedPath2Local($srcFileSyncDescriptor->fileSyncLocalPath);			
		}
		$updateData = new KalturaConvartableJobData();		
		$updateData->srcFileSyncs = $data->srcFileSyncs;		
		$job = $this->updateJob($job, null, KalturaBatchJobStatus::QUEUED, $updateData);
	
		// creates a temp file path
//		$uniqid = uniqid("convert_{$job->entryId}_");
		$uniqid = uniqid();
		$uniqid = "convert_{$job->entryId}_".substr($uniqid,-5);
		$data->destFileSyncLocalPath = $this->localTempPath . DIRECTORY_SEPARATOR . $uniqid;
		
		$this->operationEngine = KOperationManager::getEngine($job->jobSubType, $data, $job);
		
		if ( $this->operationEngine == null )
		{
			$err = "Cannot find operation engine [{$job->jobSubType}] for job id [{$job->id}]";
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::ENGINE_NOT_FOUND, $err, KalturaBatchJobStatus::FAILED);
		}
		
		KalturaLog::info( "Using engine: " . get_class($this->operationEngine) );
		
		return $this->convertImpl($job, $data);
	}
	
	protected function convertImpl(KalturaBatchJob $job, KalturaConvartableJobData $data)
	{
		return $this->convertJob($job, $data);
	}
		
	protected function convertJob(KalturaBatchJob $job, KalturaConvertJobData $data)
	{
		// ASSUME:
		// 1. full input file path for each ($data->srcFileSyncs actualFileSyncLocalPath)
		// 2. flavorParams ($data->flavorParams)
		// PROMISE
		// 1. full output file path ($data->destFileSyncLocalPath)
		// 2. full output log path
		// 3. in case of remote engine (almost done) - id/url to query result
 
// TODO: need to verify that this part can be removed
//		if($job->executionAttempts > 1) // is a retry
//		{
//			if(strlen($data->destFileSyncLocalPath) && file_exists($data->destFileSyncLocalPath))
//			{
//				return $this->moveFile($job, $data);
//			}
//		}
		$fetchFirst = null;
		foreach ($data->srcFileSyncs as $srcFileSyncDescriptor) 
		{		
			if(self::$taskConfig->params->isRemoteInput || !strlen(trim($srcFileSyncDescriptor->actualFileSyncLocalPath))) // for distributed conversion
			{
				if(!strlen(trim($srcFileSyncDescriptor->actualFileSyncLocalPath)))
					$srcFileSyncDescriptor->actualFileSyncLocalPath = self::$taskConfig->params->localFileRoot . DIRECTORY_SEPARATOR . basename($srcFileSyncDescriptor->fileSyncRemoteUrl);
					
				$err = null;
				$fetched = false;
				if(!$this->distributedFileManager->getLocalPath($srcFileSyncDescriptor->actualFileSyncLocalPath, $srcFileSyncDescriptor->fileSyncRemoteUrl, $err))
				{
					if(!$err)
						$err = 'Failed to translate url to local path';
					return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::REMOTE_FILE_NOT_FOUND, $err, KalturaBatchJobStatus::RETRY);
				}
				$fetchFirst = ($fetchFirst === null) ? $fetched : $fetchFirst;
			}
			if(!$data->flavorParamsOutput->sourceRemoteStorageProfileId)
			{
				if(!file_exists($srcFileSyncDescriptor->actualFileSyncLocalPath))
					return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $srcFileSyncDescriptor->actualFileSyncLocalPath does not exist", KalturaBatchJobStatus::RETRY);
				
				if(!self::$taskConfig->params->skipSourceValidation && !is_file($srcFileSyncDescriptor->actualFileSyncLocalPath))
					return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $srcFileSyncDescriptor->actualFileSyncLocalPath is not a file", KalturaBatchJobStatus::FAILED);
			}
			
		}
				
		$data->logFileSyncLocalPath = "{$data->destFileSyncLocalPath}.log";
		$monitorFiles = array(
			$data->logFileSyncLocalPath
		);
		$this->startMonitor($monitorFiles);
		
		$operator = $this->getOperator($data);
		try
		{
			list($actualFileSyncLocalPath, $key) = self::getFirstFilePathAndKey($data->srcFileSyncs);
			if ($fetchFirst) // if fetch with curl then the file is not encrypted
				$key = null;

			//TODO: in future remove the inFilePath parameter from operate method, the input files passed to operation
			//engine as part of the data
			$isDone = $this->operate($operator, $actualFileSyncLocalPath, null, $key);

			$data = $this->operationEngine->getData(); //get the data from operation engine for the cases it was changed
			
			$this->stopMonitor();
				
			$jobMessage = "engine [" . get_class($this->operationEngine) . "] converted successfully. ";
			
			if(!$isDone)
			{
				return $this->closeJob($job, null, null, $jobMessage, KalturaBatchJobStatus::ALMOST_DONE, $data);
			}
			else
			{
				$job = $this->updateJob($job, $jobMessage, KalturaBatchJobStatus::MOVEFILE, $data);
				return $this->moveFile($job, $data);
			}
		}
		catch (Exception $e)
		{
			$data = $this->operationEngine->getData();
			$log = $this->operationEngine->getLogData();
			//removing unsuported XML chars
			$log  = preg_replace('/[^\t\n\r\x{20}-\x{d7ff}\x{e000}-\x{fffd}\x{10000}-\x{10ffff}]/u','',$log);
			if($log && strlen($log))
			{
				try
				{
					self::$kClient->batch->logConversion($data->flavorAssetId, $log);
				}
				catch(Exception $ee)
				{
					KalturaLog::err("Log conversion: " . $ee->getMessage());
				}
			}
			$err = "engine [" . get_class($this->operationEngine) . "] converted failed: " . $e->getMessage();
			
			if ($e instanceof KOperationEngineException)
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CONVERSION_FAILED, $err, KalturaBatchJobStatus::FAILED, $data);
			//if this is not the usual KOperationEngineException, pass the Exception
			throw $e;
		}
	}
	

	protected function getOperator(KalturaConvartableJobData $data)
	{
		if(isset($data->flavorParamsOutput))
		{
			$operatorsSet = new kOperatorSets();
			$operatorsSet->setSerialized(/*stripslashes*/($data->flavorParamsOutput->operators));
			return $operatorsSet->getOperator($data->currentOperationSet, $data->currentOperationIndex);
		}
		else
			return null;
	}
	
	private function moveFile(KalturaBatchJob $job, KalturaConvertJobData $data)
	{
		$uniqid = uniqid("convert_{$job->entryId}_");
		$sharedFile = $this->sharedTempPath . DIRECTORY_SEPARATOR . $uniqid;
				
		if(!$data->flavorParamsOutput->sourceRemoteStorageProfileId)
		{
			$destFileExists = false;			
			if(count($data->extraDestFileSyncs))
			{
				$this->moveExtraFiles($data, $sharedFile);
			}			
			if($data->destFileSyncLocalPath)
			{
				clearstatcache();
				$directorySync = is_dir($data->destFileSyncLocalPath);
				if($directorySync)
					$fileSize=KBatchBase::foldersize($data->destFileSyncLocalPath);
				else
					$fileSize = kFile::fileSize($data->destFileSyncLocalPath);
			
				kFile::moveFile($data->destFileSyncLocalPath, $sharedFile);
			
				// directory sizes may differ on different devices
				if(!file_exists($sharedFile) || (is_file($sharedFile) && kFile::fileSize($sharedFile) != $fileSize))
				{
					KalturaLog::err("Error: moving file failed");
					die();
				}			
				$data->destFileSyncLocalPath = $this->translateLocalPath2Shared($sharedFile);
				if(self::$taskConfig->params->isRemoteOutput) // for remote conversion
					$data->destFileSyncRemoteUrl = $this->distributedFileManager->getRemoteUrl($data->destFileSyncLocalPath);
				else if ($this->checkFileExists($data->destFileSyncLocalPath, $fileSize, $directorySync))
					$destFileExists = true;
			}
			if(self::$taskConfig->params->isRemoteOutput) // for remote conversion
			{
				$job->status = KalturaBatchJobStatus::ALMOST_DONE;
				$job->message = "File ready for download";
			}
			elseif(!$data->destFileSyncLocalPath || $destFileExists)
			{
				$job->status = KalturaBatchJobStatus::FINISHED;
				$job->message = "File moved to shared";
			}
			else
			{
				$job->status = KalturaBatchJobStatus::RETRY;
				$job->message = "File not moved correctly";
			}
		}
		else
		{
			$job->status = KalturaBatchJobStatus::FINISHED;
			$job->message = "File is ready in the remote storage";
		}
		
		if($data->logFileSyncLocalPath && file_exists($data->logFileSyncLocalPath))
		{
			kFile::moveFile($data->logFileSyncLocalPath, "$sharedFile.log");
			$this->setFilePermissions("$sharedFile.log");
			$data->logFileSyncLocalPath = $this->translateLocalPath2Shared("$sharedFile.log");
		
			if(self::$taskConfig->params->isRemoteOutput) // for remote conversion
				$data->logFileSyncRemoteUrl = $this->distributedFileManager->getRemoteUrl($data->logFileSyncLocalPath);
		}
		else
		{
			$data->logFileSyncLocalPath = '';
		}
		
		return $this->closeJob($job, null, null, $job->message, $job->status, $data);
	}
	
	private function moveExtraFiles(KalturaConvertJobData &$data, $sharedFile)
	{
		$i=0;
		foreach ($data->extraDestFileSyncs as $destFileSync) 
		{
			$i++;
			clearstatcache();
			$directorySync = is_dir($destFileSync->fileSyncLocalPath);
			if($directorySync)
				$fileSize=KBatchBase::foldersize($destFileSync->fileSyncLocalPath);
			else
				$fileSize = kFile::fileSize($destFileSync->fileSyncLocalPath);
				
			$ext = pathinfo($destFileSync->fileSyncLocalPath, PATHINFO_EXTENSION);
			if($ext)
				$newName = $sharedFile.'.'.$ext;
			else
				$newName = $sharedFile.'.'.$i;
				
			kFile::moveFile($destFileSync->fileSyncLocalPath, $newName);
			
			// directory sizes may differ on different devices
			if(!file_exists($newName) || (is_file($newName) && kFile::fileSize($newName) != $fileSize))
			{
				KalturaLog::err("Error: moving file failed");
				die();
			}
			
			$destFileSync->fileSyncLocalPath = $this->translateLocalPath2Shared($newName);
			
			if(self::$taskConfig->params->isRemoteOutput) // for remote conversion
			{
				$destFileSync->fileSyncRemoteUrl = $this->distributedFileManager->getRemoteUrl($destFileSync->fileSyncLocalPath);
			}					
		}
	}

	protected static function getFirstFilePathAndKey($srcFileSyncs)
	{
		$actualFileSyncLocalPath = null;
		$key = null;
		$srcFileSyncDescriptor = reset($srcFileSyncs);
		if($srcFileSyncDescriptor)
		{
			$actualFileSyncLocalPath = $srcFileSyncDescriptor->actualFileSyncLocalPath;
			$key = $srcFileSyncDescriptor->fileEncryptionKey;
		}
		return array($actualFileSyncLocalPath, $key);
	}

	protected function operate($operator = null, $filePath, $configFilePath = null, $key = null)
	{
		$this->operationEngine->setEncryptionKey($key);
		if (!$key || is_dir($filePath))
			$res = $this->operationEngine->operate($operator, $filePath, $configFilePath);
		else
		{
			$tempClearPath = self::createTempClearFile($filePath, $key);
			$res = $this->operationEngine->operate($operator, $tempClearPath, $configFilePath);
			unlink($tempClearPath);
		}
		return $res;
	}
}
