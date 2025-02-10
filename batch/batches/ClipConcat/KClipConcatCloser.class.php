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
		$children = KBatchBase::$kClient->batch->getAllChildJobs($job->id);
		$doneStatuses = array(KalturaBatchJobStatus::FINISHED, KalturaBatchJobStatus::FAILED, KalturaBatchJobStatus::ABORTED);
		foreach ($children as $child)
		{
             if (!in_array($child->status, $doneStatuses))
             {
 				return $this->checkTimeout($job);
             }
         }
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::FINISHED);
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