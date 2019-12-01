<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */

/**
 * Will close a clip concat jobs that wasn't finished in the configured max time
 * @package Scheduler
 * @subpackage ClipConcat
 */
class KClipConcatCloser extends KJobCloserWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLIP_CONCAT;
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
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
		}

		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE);
	}
}