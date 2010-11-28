<?php
require_once("bootstrap.php");
/**
 * Will export a single file to ftp or scp server 
 *
 * @package Scheduler
 * @subpackage Storage
 */
class KAsyncStorageExport extends KBatchBase
{
	/**
	 * @return number
	 */
	public static function getType()
	{
		return KalturaBatchJobType::STORAGE_EXPORT;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	protected function getFilter()
	{
		$filter = parent::getFilter();
		$filter->jobSubTypeIn = $this->getSupportedProtocols();
		
		if($this->taskConfig->params)
		{
			if($this->taskConfig->params->minFileSize && is_numeric($this->taskConfig->params->minFileSize))
				$filter->fileSizeGreaterThan = $this->taskConfig->params->minFileSize;
			
			if($this->taskConfig->params->maxFileSize && is_numeric($this->taskConfig->params->maxFileSize))
				$filter->fileSizeLessThan = $this->taskConfig->params->maxFileSize;
		}
			
		return $filter;
	}
	
	public function run($jobs = null)
	{
		KalturaLog::info("Net-Storage Export batch is running");
		
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		if(is_null($jobs))
			$jobs = $this->kClient->batch->getExclusiveStorageExportJobs($this->getExclusiveLockKey(), $this->taskConfig->maximumExecutionTime, 1, $this->getFilter());
		
		KalturaLog::info(count($jobs) . " export jobs to perform");
		
		if(! count($jobs))
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return null;
		}
		
		foreach($jobs as &$job)
		{
			try
			{
				$job = $this->export($job, $job->data);
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
	
	/**
	 * Will take a single KalturaBatchJob and export the given file 
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaStorageExportJobData $data
	 * @return KalturaBatchJob
	 */
	protected function export(KalturaBatchJob $job, KalturaStorageExportJobData $data)
	{
		KalturaLog::debug("export($job->id)");
		
		$srcFile = str_replace('//', '/', trim($data->srcFileSyncLocalPath));
		
		if(!file_exists($srcFile))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $srcFile does not exist", KalturaBatchJobStatus::RETRY);
		
		if(!is_file($srcFile))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $srcFile is not a file", KalturaBatchJobStatus::FAILED);
			
		$destFile = str_replace('//', '/', trim($data->destFileSyncStoredPath));
		$this->updateJob($job, "Exporting $srcFile to $destFile", KalturaBatchJobStatus::QUEUED, 1);

		$engine = kFileTransferMgr::getInstance($job->jobSubType);
		
		try{
			$engine->login($data->serverUrl, $data->serverUsername, $data->serverPassword, null, $data->ftpPassiveMode);
			$engine->putFile($destFile, $srcFile, $data->force);
		}
		catch(kFileTransferMgrException $ke)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $ke->getCode(), $ke->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		catch(Exception $e)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $e->getCode(), $e->getMessage(), KalturaBatchJobStatus::FAILED);
		}
	
		if($this->taskConfig->params->chmod)
		{
			try{
				$engine->chmod($destFile, $this->taskConfig->params->chmod);
			}
			catch(Exception $e){}
		}
		
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{
		return $this->kClient->batch->updateExclusiveStorageExportJob($jobId, $this->getExclusiveLockKey(), $job);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
	
		$response = $this->kClient->batch->freeExclusiveStorageExportJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
	
	/*
	 * @return string
	 */
	protected function getSupportedProtocols()
	{
		$supported_engines_arr = array();
		if  ( $this->taskConfig->params->useFTP ) $supported_engines_arr[] = KalturaExportProtocol::FTP;
		if  ( $this->taskConfig->params->useSCP ) $supported_engines_arr[] = KalturaExportProtocol::SCP;
		if  ( $this->taskConfig->params->useSFTP ) $supported_engines_arr[] = KalturaExportProtocol::SFTP;
		
		return join(',', $supported_engines_arr);
	}
}
?>