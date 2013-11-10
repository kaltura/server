<?php

class KAsyncDropFolderContentProcessor extends KJobHandlerWorker
{
	/**
	 * @var KalturaDropFolderClientPlugin
	 */
	protected $dropFolderPlugin = null;
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::DROP_FOLDER_CONTENT_PROCESSOR;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		try 
		{
			return $this->process($job, $job->data);
		}
		catch(kTemporaryException $e)
		{
			$this->unimpersonate();
			if($e->getResetJobExecutionAttempts())
				throw $e;
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $e->getCode(), "Error: " . $e->getMessage(), KalturaBatchJobStatus::FAILED);
		}
		catch(KalturaClientException $e)
		{
			$this->unimpersonate();
			return $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_CLIENT, $e->getCode(), "Error: " . $e->getMessage(), KalturaBatchJobStatus::FAILED);
		}
	}

	protected function process(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		$job = $this->updateJob($job, "Start processing drop folder files [$data->dropFolderFileIds]", KalturaBatchJobStatus::QUEUED);
		$engine = KDropFolderEngine::getInstance($job->jobSubType);
		$engine->processFolder($job, $data);
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
	}
		
}
