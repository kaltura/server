<?php
/**
 * @package Scheduler
 * @subpackage RecalculateCache
 */

/**
 * Will recalculate cached objects 
 *
 * @package Scheduler
 * @subpackage RecalculateCache
 */
class KAsyncRecalculateCache extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::RECALCULATE_CACHE;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->recalculate($job, $job->data);
	}
	
	private function recalculate(KalturaBatchJob $job, KalturaRecalculateCacheJobData $data)
	{
		$engine = KRecalculateCacheEngine::getInstance($job->jobSubType);
		$recalculatedObjects = $engine->recalculate($data);
		return $this->closeJob($job, null, null, "Recalculated $recalculatedObjects cache objects", KalturaBatchJobStatus::FINISHED);
	}
}
