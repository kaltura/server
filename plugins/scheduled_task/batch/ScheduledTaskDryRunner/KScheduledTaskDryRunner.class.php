<?php
/**
 * @package plugins.scheduledTask
 * @subpackage Scheduler
 */
class KScheduledTaskDryRunner extends KJobHandlerWorker
{
	const TEMP_SHARD_PATH = "sharedTempPath";

	private $sharedFilePath;
	private $tempFilePath;

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

	private function initClient($jobData, $partnerId)
	{
		$client = $this->getClient();
		$ks = $this->createKs($client, $jobData);
		$client->setKs($ks);
		$this->impersonate($partnerId);
		return $client;
	}

	private function getFileHandler()
	{
		$sharedPath = $this->getAdditionalParams(self::TEMP_SHARD_PATH);
		KalturaLog::info('Temp shared path: '.$sharedPath);
		if (!is_dir($sharedPath))
		{
			kFile::fullMkfileDir($sharedPath);
			if (!is_dir($sharedPath))
				throw new Exception('Shared path ['.$sharedPath.'] does not exist and could not be created');
		}
		$fileName = uniqid('sheduledtask_');
		$this->sharedFilePath = $sharedPath.DIRECTORY_SEPARATOR.$fileName;
		$this->tempFilePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.$fileName;
		$handle = fopen($this->tempFilePath, "w");
		KalturaLog::info('Temp file: '.$this->tempFilePath);
		return $handle;
	}

	/**
	 * @return KalturaScheduledTaskProfile
	 */
	private function getScheduledTaskProfile($profileId)
	{
		$client = $this->getClient();
		$scheduledTaskClient = KalturaScheduledTaskClientPlugin::get($client);
		return $scheduledTaskClient->scheduledTaskProfile->get($profileId);
	}

	private function createKs(KalturaClient $client, KalturaScheduledTaskJobData $jobData)
	{
		$partnerId = self::$taskConfig->getPartnerId();
		$sessionType = KalturaSessionType::ADMIN;
		$puserId = 'batchUser';
		$adminSecret = self::$taskConfig->getSecret();
		$privileges = array('disableentitlement');
		if ($jobData->referenceTime)
			$privileges[] = 'reftime:'.$jobData->referenceTime;

		return $client->generateSession($adminSecret, $puserId, $sessionType, $partnerId, 86400, implode(',', $privileges));
	}

	private function execDryRun(KalturaBatchJob $job, KalturaScheduledTaskJobData $jobData)
	{
		$handle = $this->getFileHandler();
		$profileId = $job->jobObjectId;
		$maxResults = ($jobData->maxResults) ? $jobData->maxResults : 500;
		$scheduledTaskProfile = $this->getScheduledTaskProfile($profileId);
		$client = $this->initClient($jobData,$scheduledTaskProfile->partnerId);
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;
		$resultsCount = 0;
		$filter = $scheduledTaskProfile->objectFilter;

		while(true)
		{
			try
			{
				$results = ScheduledTaskBatchHelper::query($client, $scheduledTaskProfile, $pager, $filter);
				$objects = $results->objects;
				$count = count($objects);
				if (!$count)
					break;

				$resultsCount += $count;
				foreach ($objects as $object)
					fwrite($handle, serialize($object).ScheduledTaskBatchHelper::getDryRunObjectSeprator());
			}
			catch(Exception $ex)
			{
				$this->unimpersonate();
				throw $ex;
			}

			if ($resultsCount >= $maxResults || $resultsCount < 500)
				break;

			$lastResult = end($objects);
			$filter->createdAtGreaterThanOrEqual = $lastResult->createdAt;
			$filter->idNotIn = ScheduledTaskBatchHelper::getEntriesIdWithSameCreateAtTime($objects, $lastResult->createdAt);
		}

		$this->unimpersonate();
		fwrite ($handle, "Total results: {$resultsCount}".PHP_EOL);
		fclose($handle);
		$jobData->totalCount = $resultsCount;
		$jobData->isNewFormat = true;
		kFile::moveFile($this->tempFilePath, $this->sharedFilePath);
		KalturaLog::info('Temp shared path: '.$this->tempPath);
		$jobData->resultsFilePath = $this->sharedFilePath;
		return $this->closeJob($job, null, null, 'Dry run finished', KalturaBatchJobStatus::FINISHED, $jobData);
	}
	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function exec(KalturaBatchJob $job)
	{
		return $this->execDryRun($job, $job->data);
	}
}
