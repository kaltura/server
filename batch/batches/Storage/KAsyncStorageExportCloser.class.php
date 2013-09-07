<?php
class KAsyncStorageExportCloser extends KJobCloserWorker
{
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job) {
		$this->closeStorageExport($job);
		
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	protected function getJobType() {
		return self::getType();
	}

	public static function getType()
	{
		return KalturaBatchJobType::STORAGE_EXPORT;
	}
	
	protected function closeStorageExport (KalturaBatchJob $job)
	{
		KalturaLog::info("Attempting to close the job");
		$storageExportEngine = KExportEngine::getInstance($job->jobSubType, $job->partnerId, $job->data);
		
		$closeResult = $storageExportEngine->verifyExportedResource();
		return $this->closeJob($job, null, null, null, $closeResult ? KalturaBatchJobStatus::FINISHED : KalturaBatchJobStatus::ALMOST_DONE);
	}
}