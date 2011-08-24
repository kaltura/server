<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */
require_once("bootstrap.php");

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
		return $this->checkTimeout($job);
	}

	private function checkTimeout(KalturaBatchJob $job)
	{
		if($job->queueTime && ($job->queueTime + $this->taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
			
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE);
	}
}
