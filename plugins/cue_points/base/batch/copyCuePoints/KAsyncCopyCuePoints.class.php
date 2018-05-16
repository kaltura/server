<?php
/**
 * @package Scheduler
 * @subpackage copyCuePoints
 */

class KAsyncCopyCuePoints extends KJobHandlerWorker
{
	const MAX_CUE_POINTS_TO_COPY_TO_VOD = 500;

	/*
	 * (non-PHPdoc)
	 *  @see KBatchBase::getJobType();
	 */
	const ATTEMPT_ALLOWED = 3;

	public static function getType()
	{
		return KalturaBatchJobType::COPY_CUE_POINTS;
	}

	/*
	 * (non-PHPdoc)
	 *  @see KBatchBase::getJobType();
	 */
	public static function getJobType()
	{
		return KalturaBatchJobType::COPY_CUE_POINTS;
	}


	/**
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob
	 */
	protected function exec(KalturaBatchJob $job)
	{
		$engine = KCopyCuePointEngine::getEngine($job->jobSubType, $job->data, $job->partnerId);
		if (!$engine)
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::ENGINE_NOT_FOUND,
							"Cannot find copy engine [{$job->jobSubType}]", KalturaBatchJobStatus::FAILED);
		if (!$engine->validateJobData())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::MISSING_PARAMETERS,
				"Job subType [{$job->jobSubType}] has missing job data", KalturaBatchJobStatus::FAILED);
		if (!$engine->copyCuePoints())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, null,
				"Job has failed in copy the cue points", KalturaBatchJobStatus::FAILED);

		return $this->closeJob($job, null, null, "All Cue Point Copied ", KalturaBatchJobStatus::FINISHED);
	}

}
