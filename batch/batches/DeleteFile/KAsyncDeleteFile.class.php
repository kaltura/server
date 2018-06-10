<?php
/**
 * This worker deletes physical files from disk
 *
 * @package Scheduler
 * @subpackage Delete
 */
class KAsyncDeleteFile extends KJobHandlerWorker
{
	public static function getType()
	{
		return KalturaBatchJobType::DELETE_FILE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		$this->updateJob($job, "File deletion started", KalturaBatchJobStatus::PROCESSING);
		$jobData = $job->data;
		
		if (!is_file($jobData->localFileSyncPath))
			return $this->closeJob($job, null, null, 'File already deleted', KalturaBatchJobStatus::FINISHED);
		
		/* @var $jobData KalturaDeleteFileJobData */
		$result = unlink($jobData->localFileSyncPath);
		
		if (!$result)
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, null, "Failed to delete file from disk", KalturaBatchJobStatus::FAILED);
		
		return $this->closeJob($job, null, null, 'File deleted successfully', KalturaBatchJobStatus::FINISHED);
		
	}


}
