<?php
/**
 * @package Scheduler
 * @subpackage Storage
 */

/**
 * Will export a single file to ftp or scp server 
 *
 * @package Scheduler
 * @subpackage Storage
 */
class KAsyncStorageExport extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::STORAGE_EXPORT;
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
		return $this->export($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getMaxJobsEachRun()
	 */
	protected function getMaxJobsEachRun()
	{
		return 1;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getFilter()
	 */
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
		}
		catch(Exception $e)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $e->getCode(), $e->getMessage(), KalturaBatchJobStatus::RETRY);
		}
	
		try{
			$engine->putFile($destFile, $srcFile, $data->force);
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
	
	/*
	 * @return string
	 */
	protected function getSupportedProtocols()
	{
		$supported_engines_arr = array();
		if  ( $this->taskConfig->params->useFTP ) $supported_engines_arr[] = KalturaExportProtocol::FTP;
		if  ( $this->taskConfig->params->useSCP ) $supported_engines_arr[] = KalturaExportProtocol::SCP;
		if  ( $this->taskConfig->params->useSFTP ) $supported_engines_arr[] = KalturaExportProtocol::SFTP;
		if  ( $this->taskConfig->params->useS3 ) $supported_engines_arr[] = KalturaExportProtocol::S3;
		
		return join(',', $supported_engines_arr);
	}
}
?>