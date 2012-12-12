<?php
/**
 * @package Scheduler
 * @subpackage Storage
 */

/**
 * Will delete a single file to ftp or scp server 
 *
 * @package Scheduler
 * @subpackage Storage
 */
class KAsyncStorageDelete extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::STORAGE_DELETE;
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
		return $this->delete($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getFilter()
	 */
	protected function getFilter()
	{
		$filter = parent::getFilter();
		
		if(is_null($filter->jobSubTypeIn))
			$filter->jobSubTypeIn = $this->getSupportedProtocols();
		
		if($this->taskConfig->params->minFileSize && is_numeric($this->taskConfig->params->minFileSize))
			$filter->fileSizeGreaterThan = $this->taskConfig->params->minFileSize;
		
		if($this->taskConfig->params->maxFileSize && is_numeric($this->taskConfig->params->maxFileSize))
			$filter->fileSizeLessThan = $this->taskConfig->params->maxFileSize;
			
		return $filter;
	}
	
	/**
	 * Will take a single KalturaBatchJob and delete the given file 
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaStorageDeleteJobData $data
	 * @return KalturaBatchJob
	 */
	private function delete(KalturaBatchJob $job, KalturaStorageDeleteJobData $data)
	{
		KalturaLog::debug("delete($job->id)");
		
		$srcFile = str_replace('//', '/', trim($data->srcFileSyncLocalPath));
		$destFile = str_replace('//', '/', trim($data->destFileSyncStoredPath));
		$this->updateJob($job, "Deleting $srcFile to $destFile", KalturaBatchJobStatus::QUEUED);

		$engineOptions = isset($this->taskConfig->engineOptions) ? $this->taskConfig->engineOptions->toArray() : array();
		$engineOptions['passiveMode'] = $data->ftpPassiveMode;
		$engine = kFileTransferMgr::getInstance($job->jobSubType);
		
		try{
			$engine->login($data->serverUrl, $data->serverUsername, $data->serverPassword);
			$engine->delFile($destFile);
		}
		catch(kFileTransferMgrException $ke)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, $ke->getCode(), $ke->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		catch(Exception $e)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $e->getCode(), $e->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
	}
	
	/*
	 * @return string
	 */
	protected function getSupportedProtocols()
	{ 
		$supported_engines_arr = array();
		if  ( $this->taskConfig->params->useFTP ) $supported_engines_arr[] = KalturaStorageProfileProtocol::FTP;
		if  ( $this->taskConfig->params->useSCP ) $supported_engines_arr[] = KalturaStorageProfileProtocol::SCP;
		if  ( $this->taskConfig->params->useSFTP ) $supported_engines_arr[] = KalturaStorageProfileProtocol::SFTP;
		
		return join(',', $supported_engines_arr);
	}
}
