<?php

/**
 * Enter description here ...
 * @author Hila
 *
 */
class KAsyncDeleteFile extends KJobHandlerWorker
{
	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	protected function getJobType()
	{
		return KalturaBatchJobType::DELETE_FILE;
	}
	
	public static function getType()
	{
		return  self::getJobType();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		$jobData = $job->data;
		
		/* @var $jobData KalturaDeleteFileJobData */
		unlink($jobData->localFileSyncPath);
		
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
		
	}


}