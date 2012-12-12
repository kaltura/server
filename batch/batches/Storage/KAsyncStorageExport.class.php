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
		
		if(!$this->pollingFileExists($srcFile))
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, "Source file $srcFile does not exist", KalturaBatchJobStatus::RETRY);
					
		$destFile = str_replace('//', '/', trim($data->destFileSyncStoredPath));
		$this->updateJob($job, "Exporting $srcFile to $destFile", KalturaBatchJobStatus::QUEUED);

		$engineOptions = isset($this->taskConfig->engineOptions) ? $this->taskConfig->engineOptions->toArray() : array();
		$engineOptions['passiveMode'] = $data->ftpPassiveMode;
		if($data instanceof KalturaAmazonS3StorageExportJobData)
			$engineOptions['filesAcl'] = $data->filesPermissionInS3;
			
		$engine = kFileTransferMgr::getInstance($job->jobSubType, $engineOptions);
		
		try{
			$engine->login($data->serverUrl, $data->serverUsername, $data->serverPassword);
		}
		catch(Exception $e)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $e->getCode(), $e->getMessage(), KalturaBatchJobStatus::RETRY);
		}
	
		try{
			if (is_file($srcFile)){
				$engine->putFile($destFile, $srcFile, $data->force);
			}
			else if (is_dir($srcFile)){
				$filesPaths = kFile::dirList($srcFile);
				$destDir = $destFile;
				foreach ($filesPaths as $filePath){
					$destFile = $destDir.DIRECTORY_SEPARATOR.basename($filePath);
					$engine->putFile($destFile, $filePath, $data->force);
				}
			}
		}
		catch(kFileTransferMgrException $e){
			if($e->getCode() == kFileTransferMgrException::remoteFileExists)
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::FILE_ALREADY_EXISTS,$e->getMessage(),KalturaBatchJobStatus::FAILED);
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $e->getCode(), $e->getMessage(), KalturaBatchJobStatus::FAILED);
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
}
