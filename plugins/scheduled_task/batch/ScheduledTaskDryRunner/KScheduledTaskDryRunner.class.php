<?php
/**
 * @package plugins.scheduledTask
 * @subpackage Scheduler
 */
class KScheduledTaskDryRunner extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::SCHEDULED_TASK;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function exec(KalturaBatchJob $job)
	{
		$sharedPath = $this->getAdditionalParams('sharedTempPath');
		if (!is_dir($sharedPath))
		{
			kFile::fullMkfileDir($sharedPath);
			if (!is_dir($sharedPath))
				throw new Exception('Shared path ['.$sharedPath.'] doesn\'t exist and could not be created');
		}

		KalturaLog::debug('Temp shared path: '.$sharedPath);

		/** @var KalturaScheduledTaskJobData $jobData */
		$jobData = $job->data;
		$profileId = $job->jobObjectId;
		$maxResults = ($jobData->maxResults) ? $jobData->maxResults : 500;
		$scheduledTaskClient = $this->getScheduledTaskClient();
		$scheduledTaskProfile = $scheduledTaskClient->scheduledTaskProfile->get($profileId);

		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;

		$response = new KalturaObjectListResponse();
		$response->objects = array();
		$response->totalCount = 0;
		$resultsCount = 0;
		while(true)
		{
			$results = ScheduledTaskBatchHelper::query($this->getClient(), $scheduledTaskProfile, $pager);
			if (!count($results->objects))
				break;

			$resultsCount += count($results->objects);
			if ($resultsCount >= $maxResults)
				break;

			$response->objects = array_merge($response->objects, $results->objects);
			$response->totalCount+= count($results->objects);
			$pager->pageIndex++;
		}

		$sharedFilePath = $sharedPath.'/'.uniqid('sheduledtask_');
		KalturaLog::debug('Temp shared file: '.$sharedFilePath);
		file_put_contents($sharedFilePath, serialize($response));
		$jobData->resultsFilePath = $sharedFilePath;
		return $this->closeJob($job, null, null, 'Dry run finished', KalturaBatchJobStatus::FINISHED, $jobData);
	}

	/**
	 * @return KalturaScheduledTaskClientPlugin
	 */
	protected function getScheduledTaskClient()
	{
		$client = $this->getClient();
		return KalturaScheduledTaskClientPlugin::get($client);
	}
}
