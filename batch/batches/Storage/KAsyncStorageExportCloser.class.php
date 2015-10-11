<?php
class KAsyncStorageExportCloser extends KJobCloserWorker
{
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job) {
		$this->closeStorageExport($job);
		
	}

	public static function getType()
	{
		return KalturaBatchJobType::STORAGE_EXPORT;
	}
	
	protected function closeStorageExport (KalturaBatchJob $job)
	{
		$storageExportEngine = KExportEngine::getInstance($job->jobSubType, $job->partnerId, $job->data);
		
		$closeResult = $storageExportEngine->verifyExportedResource();
		$this->closeJob($job, null, null, null, $closeResult ? KalturaBatchJobStatus::FINISHED : KalturaBatchJobStatus::ALMOST_DONE);
	}
}