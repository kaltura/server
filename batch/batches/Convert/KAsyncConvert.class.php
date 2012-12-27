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
		return $this->convert($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getFilter()
	 */
	protected function getFilter()
	{
		$filter = parent::getFilter();
		
		if($this->taskConfig->params->minFileSize && is_numeric($this->taskConfig->params->minFileSize))
			$filter->fileSizeGreaterThan = $this->taskConfig->params->minFileSize;
		
		if($this->taskConfig->params->maxFileSize && is_numeric($this->taskConfig->params->maxFileSize))
			$filter->fileSizeLessThan = $this->taskConfig->params->maxFileSize;
			
		return $filter;
	}
		
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::run()
	 */
	public function run($jobs = null)
	{
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
		
		$remoteFileRoot = $this->taskConfig->getRemoteServerUrl() . $this->taskConfig->params->remoteUrlDirectory;
		$this->distributedFileManager = new KDistributedFileManager($this->taskConfig->params->localFileRoot, $remoteFileRoot, $this->taskConfig->params->fileCacheExpire);
		
		return parent::run($jobs);
	}
	
	protected function convert(KalturaBatchJob $job, KalturaConvartableJobData $data)
	{
			/*
			 * When called for 'collections', the 'flavorParamsOutputId' is not set.
			 * It is set in the 'flavors' array, but for collections the 'flavorParamsOutput' it is unrequired.
			 */
		if(isset($data->flavorParamsOutputId))
			$data->flavorParamsOutput = $this->kClient->flavorParamsOutput->get($data->flavorParamsOutputId);
		
		$data->actualSrcFileSyncLocalPath = $this->translateSharedPath2Local($data->srcFileSyncLocalPath);
		$updateData = new KalturaConvartableJobData();
		$updateData->actualSrcFileSyncLocalPath = $data->actualSrcFileSyncLocalPath;
		$job = $this->updateJob($job, null, KalturaBatchJobStatus::QUEUED, $updateData);
	
		// creates a temp file path
//		$uniqid = uniqid("convert_{$job->entryId}_");
		$uniqid = uniqid();
		$uniqid = "convert_{$job->entryId}_".substr($uniqid,-5);
		$data->destFileSyncLocalPath = $this->localTempPath . DIRECTORY_SEPARATOR . $uniqid;
		
		$this->operationEngine = KOperationManager::getEngine($job->jobSubType, $this->taskConfig, $data, $this->kClient);
		
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
		KalturaLog::info("Converting flavor job");
		
		// ASSUME:
		// 1. full input file path ($data->actualSrcFileSyncLocalPath)
		// 2. flavorParams ($data->flavorParams)
		// PROMISE
		// 1. full output file path ($data->destFileSyncLocalPath)
		// 2. full output log path
		// 3. in case of remote engine (almost done) - id/url to query result
 
	
		if($job->executionAttempts > 1) // is a retry
		{
			if(strlen($data->destFileSyncLocalPath) && file_exists($data->destFileSyncLocalPath))
			{
				return $this->moveFile($job, $data);
			}
		}
		
		if($this->taskConfig->params->isRemoteInput || !strlen(trim($data->actualSrcFileSyncLocalPath))) // for distributed conversion
		{
			if(!strlen(trim($data->actualSrcFileSyncLocalPath)))
				$data->actualSrcFileSyncLocalPath = $this->taskConfig->params->localFileRoot . DIRECTORY_SEPARATOR . basename($data->srcFileSyncRemoteUrl);
				
			$err = null;
			if(!$this->distributedFileManager->getLocalPath($data->actualSrcFileSyncLocalPath, $data->srcFileSyncRemoteUrl, $err))
			{
				if(!$err)
					$err = 'Failed to translate url to local path';
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::REMOTE_FILE_NOT_FOUND, $err, KalturaBatchJobStatus::RETRY);
			}
		}
		
		if(!$data->flavorParamsOutput->sourceRemoteStorageProfileId)
		{
			if(!file_exists($data->actualSrcFileSyncLocalPath))
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $data->actualSrcFileSyncLocalPath does not exist", KalturaBatchJobStatus::RETRY);
			
			if(!is_file($data->actualSrcFileSyncLocalPath))
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $data->actualSrcFileSyncLocalPath is not a file", KalturaBatchJobStatus::FAILED);
		}
		
		$data->logFileSyncLocalPath = "{$data->destFileSyncLocalPath}.log";
		$monitorFiles = array(
			$data->logFileSyncLocalPath
		);
		$this->startMonitor($monitorFiles);
		
		$operator = $this->getOperator($data);
		try
		{
			$this->operationEngine->operate($operator, $data->actualSrcFileSyncLocalPath);
		}
		catch(KOperationEngineException $e)
		{
			$log = $this->operationEngine->getLogData();
			//removing unsuported XML chars 
			$log  = preg_replace('/[^\t\n\r\x{20}-\x{d7ff}\x{e000}-\x{fffd}\x{10000}-\x{10ffff}]/u','',$log);
			if($log && strlen($log))
			{
				try
				{
					$this->kClient->batch->logConversion($data->flavorAssetId, $log);
				}
				catch(Exception $ee)
				{
					KalturaLog::err("Log conversion: " . $ee->getMessage());
				}
			}
			$err = "engine [" . get_class($this->operationEngine) . "] converted failed: " . $e->getMessage();
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CONVERSION_FAILED, $err, KalturaBatchJobStatus::FAILED);
		}
		$this->stopMonitor();
		
		if($job->jobSubType == KalturaConversionEngineType::ENCODING_COM || $job->jobSubType == KalturaConversionEngineType::KALTURA_COM)
		{
			$msg = $this->operationEngine->getMessage();
			$msg = "engine [" . get_class($this->operationEngine) . "] converted successfully: $msg";
			return $this->closeJob($job, null, null, $msg, KalturaBatchJobStatus::ALMOST_DONE, $data);
		}
		
		$job = $this->updateJob($job, "engine [" . get_class($this->operationEngine) . "] converted successfully", KalturaBatchJobStatus::MOVEFILE, $data);
		return $this->moveFile($job, $data);
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
		KalturaLog::debug("moveFile($job->id, $data->destFileSyncLocalPath)");
		
		$uniqid = uniqid("convert_{$job->entryId}_");
		$sharedFile = $this->sharedTempPath . DIRECTORY_SEPARATOR . $uniqid;
				
		if(!$data->flavorParamsOutput->sourceRemoteStorageProfileId)
		{
			clearstatcache();
			$fileSize = kFile::fileSize($data->destFileSyncLocalPath);
			kFile::moveFile($data->destFileSyncLocalPath, $sharedFile);
			
			// directory sizes may differ on different devices
			if(!file_exists($sharedFile) || (is_file($sharedFile) && kFile::fileSize($sharedFile) != $fileSize))
			{
				KalturaLog::err("Error: moving file failed");
				die();
			}
			
			$data->destFileSyncLocalPath = $this->translateLocalPath2Shared($sharedFile);
			
			if($this->taskConfig->params->isRemoteOutput) // for remote conversion
			{
				$data->destFileSyncRemoteUrl = $this->distributedFileManager->getRemoteUrl($data->destFileSyncLocalPath);
				$job->status = KalturaBatchJobStatus::ALMOST_DONE;
				$job->message = "File ready for download";
			}
			elseif($this->checkFileExists($data->destFileSyncLocalPath, $fileSize))
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
			@chmod("$sharedFile.log", 0777);
			$data->logFileSyncLocalPath = $this->translateLocalPath2Shared("$sharedFile.log");
		
			if($this->taskConfig->params->isRemoteOutput) // for remote conversion
				$data->logFileSyncRemoteUrl = $this->distributedFileManager->getRemoteUrl($data->logFileSyncLocalPath);
		}
		else 
		{
			$data->logFileSyncLocalPath = '';
		}
		
		return $this->closeJob($job, null, null, $job->message, $job->status, $data);
	}
}
