<?php
/**
 * @package plugins.scheduledTask
 * @subpackage Scheduler
 */
class KScheduledTaskDryRunner extends KJobHandlerWorker
{
	const SHARED_TEMP_PATH = "sharedTempPath";

	/**
	 * @var string
	 */
	private $sharedFilePath;

	/**
	 * @var string
	 */
	private $tempFilePath;

	/**
	 * @var resource
	 */
	private $handle;

	/**
	 * @var int
	 */
	private $maxResults;

	/**
	 * @var kalturaPager
	 */
	private $pager;

	/**
	 * @var kalturaFilter
	 */
	private $filter;

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

	private function initRunFiles()
	{
		$sharedPath = $this->getAdditionalParams(self::SHARED_TEMP_PATH);
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
		$this->handle = fopen($this->tempFilePath, "w");
		KalturaLog::info('Temp file: '.$this->tempFilePath);
	}

	/**
	 * @param string $profileId
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

	private function initRunData(KalturaBatchJob $job, KalturaScheduledTaskJobData $jobData)
	{
		$this->initRunFiles();
		$profileId = $job->jobObjectId;
		$this->maxResults = ($jobData->maxResults) ? $jobData->maxResults : 500;
		$scheduledTaskProfile = $this->getScheduledTaskProfile($profileId);
		$this->initClient($jobData,$scheduledTaskProfile->partnerId);
		$this->pager = new KalturaFilterPager();
		$this->pager->pageSize = 500;
		$this->pager->pageIndex = 1;
		$this->filter = $scheduledTaskProfile->objectFilter;
	}

	private function execDryRunInCSVMode($results, KalturaScheduledTaskJobData $jobData)
	{
		$resultsCount = 0;
		while(true)
		{
			try
			{
				$objects = $results->objects;
				$count = count($objects);
				if (!$count)
					break;

				$resultsCount += $count;
				foreach ($objects as $entry)
				{
					$csvEntryData = $this->getCsvData($entry);
					fputcsv($this->handle, $csvEntryData, ",");
				}
			}
			catch(Exception $ex)
			{
				$this->unimpersonate();
				throw $ex;
			}

			if ($resultsCount >= $this->maxResults || $resultsCount < 500)
				break;

			$this->updateFitler($results->objects);
			$results = ScheduledTaskHelper::query($this->client, $this->scheduledTaskProfile, $this->pager, $this->filter);
		}

		$jobData->totalCount = $resultsCount;
	}

	/**
	 * @param KalturaBaseEntry $entry
	 * @return array
	 */
	private function getCsvData($entry)
	{
		$date = gmdate("M d Y H:i:s", $entry->lastPlayedAt);
		$mediaType = ScheduledTaskHelper::getMediaTypeString($entry->mediaType);
		return array($entry->id, $entry->name, $date, $mediaType);
	}

	private function execDryRunInListResponseMode($results, KalturaScheduledTaskJobData $jobData)
	{
		$resultsCount =0;
		$response = new KalturaObjectListResponse();
		$response->objects = array();
		while(true)
		{
			$objects = $results->objects;
			$count = count($objects);
			if (!$count)
				break;

			$resultsCount += $count;
			if ($resultsCount >= $this->maxResults || $resultsCount < 500)
				break;

			$response->objects = array_merge($response->objects, $results->objects);
			$this->updateFitler($results->objects);
			$results = ScheduledTaskHelper::query($this->client, $this->scheduledTaskProfile, $this->pager, $this->filter);
		}

		$response->totalCount = $resultsCount;
		$jobData->totalCount = $resultsCount;
		try
		{
			fwrite($this->handle, serialize($response));
		}
		catch(Exception $ex)
		{
				$this->unimpersonate();
				throw $ex;
		}
	}

	private function execDryRun(KalturaBatchJob $job, KalturaScheduledTaskJobData $jobData)
	{
		$this->initRunData($job, $jobData);
		$results = ScheduledTaskHelper::query($this->client, $this->scheduledTaskProfile, $this->pager, $this->filter);
		if($results->totalCount > ScheduledTaskHelper::MAX_RESULTS_THRESHOLD)
		{
			$this->execDryRunInCSVMode($results);
		}
		else
		{
			$this->execDryRunInListResponseMode($results, $jobData);
		}

		$this->unimpersonate();
		fclose($this->handle);
		kFile::moveFile($this->tempFilePath, $this->sharedFilePath);
		KalturaLog::info('Temp shared path: '.$this->tempPath);
		$jobData->resultsFilePath = $this->sharedFilePath;
		return $this->closeJob($job, null, null, 'Dry run finished', KalturaBatchJobStatus::FINISHED, $jobData);
	}

	/**
	 * @param KalturaBaseEntryArray $entries
	 */
	private function updateFitler($entries)
	{
		$lastResult = end($entries);
		$this->filter->createdAtGreaterThanOrEqual = $lastResult->createdAt;
		$idsToIgnore = ScheduledTaskHelper::getEntriesIdWithSameCreateAtTime($entries, $lastResult->createdAt);
		$this->filter->idNotIn = implode (", ", $idsToIgnore);
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function exec(KalturaBatchJob $job)
	{
		return $this->execDryRun($job, $job->data);
	}
}
