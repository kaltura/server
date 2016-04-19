<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */

/**
 * Will close almost done conversions that sent to remote systems and store the files in the file system.
 * The state machine of the job is as follows:
 * 	 	get almost done conversions 
 * 		check the convert status
 * 		download the converted file
 * 		save recovery file in case of crash
 * 		move the file to the archive
 *
 * @package Scheduler
 * @subpackage Conversion
 */
class KAsyncConvertProfileCloser extends KJobCloserWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CONVERT_PROFILE;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->checkTimeout($job);
	}

	private function checkTimeout(KalturaBatchJob $job)
	{
		
		if($job->queueTime && ($job->queueTime + self::$taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
		else if ($this->checkConvertDone($job))
		{
			return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
		}
			
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE);
	}
	
	private function checkConvertDone(KalturaBatchJob $job)
	{
		/**
		 * @var KalturaConvertProfileJobData $data
		 */
		return self::$kClient->batch->checkEntryIsDone($job->id);
	}
}
