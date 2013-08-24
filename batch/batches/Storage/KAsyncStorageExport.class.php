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
		
		if(KBatchBase::$taskConfig->params)
		{
			if(KBatchBase::$taskConfig->params->minFileSize && is_numeric(KBatchBase::$taskConfig->params->minFileSize))
				$filter->fileSizeGreaterThan = KBatchBase::$taskConfig->params->minFileSize;
			
			if(KBatchBase::$taskConfig->params->maxFileSize && is_numeric(KBatchBase::$taskConfig->params->maxFileSize))
				$filter->fileSizeLessThan = KBatchBase::$taskConfig->params->maxFileSize;
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
		$engine = KExportEngine::getInstance($job->jobSubType, $job->partnerId, $data);
		if(!$engine)
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::ENGINE_NOT_FOUND, "Engine not found", KalturaBatchJobStatus::FAILED);
		}
		$this->updateJob($job, null, KalturaBatchJobStatus::QUEUED);
		$exportResult = $engine->export();

		return $this->closeJob($job, null , null, null, $exportResult ? KalturaBatchJobStatus::FINISHED : KalturaBatchJobStatus::ALMOST_DONE, $data );
	}
}
