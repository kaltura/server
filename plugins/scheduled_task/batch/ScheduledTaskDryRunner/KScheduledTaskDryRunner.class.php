<?php
/**
 * @package plugins.scheduledTask
 * @subpackage Scheduler
 */
class KScheduledTaskDryRunner extends KJobHandlerWorker
{
	const SHARED_TEMP_PATH = "sharedTempPath";
	const PAGE_SIZE = 500;
	const MAX_RESULTS_THRESHOLD = 1000;

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
	 * @var KalturaBaseEntryFilter
	 */
	private $filter;

	/**
	 * @var kalturaClient
	 */
	private $client;

	/**
	 * @var scheduledTaskProfile
	 */
	private $scheduledTaskProfile;

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
		$this->client = $client;
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
		$this->maxResults = ($jobData->maxResults) ? $jobData->maxResults : self::PAGE_SIZE;
		$this->scheduledTaskProfile = $this->getScheduledTaskProfile($profileId);
		$this->initClient($jobData, $this->scheduledTaskProfile->partnerId);
		$this->pager = new KalturaFilterPager();
		$this->pager->pageSize = self::PAGE_SIZE;
		$this->pager->pageIndex = 1;
		$this->filter = $this->scheduledTaskProfile->objectFilter;
	}

	private function writeEntriesToCsv($entries)
	{
		foreach ($entries as $entry)
		{
			$csvEntryData = $this->getCsvData($entry);
			fputcsv($this->handle, $csvEntryData, ",");
		}
	}

	private function execDryRunInCSVMode($firstPage, KalturaScheduledTaskJobData $jobData)
	{
		$jobData->fileFormat = KalturaDryRunFileType::CSV;
		$resultsCount = count($firstPage->objects);
		try
		{
			fputcsv($this->handle, $this->getCsvHeaders());
			$this->writeEntriesToCsv($firstPage->objects);
			$count = $resultsCount;
			$this->updateFitler($firstPage->objects);
			while($resultsCount < $this->maxResults && $count == self::PAGE_SIZE)
			{
				$results = ScheduledTaskBatchHelper::query($this->client, $this->scheduledTaskProfile, $this->pager, $this->filter);
				$objects = $results->objects;
				$count = count($objects);
				if ($count)
				{
					$resultsCount += $count;
					$this->writeEntriesToCsv($objects);
					$this->updateFitler($objects);
				}
			}
		}
		catch(Exception $ex)
		{
			$this->unimpersonate();
			throw $ex;
		}

		$jobData->totalCount = $resultsCount;
	}

	/**
	 * @param KalturaMediaEntry $entry
	 * @return array
	 */
	private function getCsvData($entry)
	{
		$date = gmdate("M d Y H:i:s", $entry->lastPlayedAt);
		$mediaType = ScheduledTaskBatchHelper::getMediaTypeString($entry->mediaType);
		return array($entry->id, $entry->name, $date, $mediaType);
	}

	/**
	 * @return array
	 */
	private function getCsvHeaders()
	{
		return array("id", "name", "last played at", "media type");
	}


	/**
	 * @param KalturaBaseEntryListResponse $firstPage
	 * @param KalturaScheduledTaskJobData $jobData
	 * @throws Exception
	 */
	private function execDryRunInListResponseMode($firstPage, KalturaScheduledTaskJobData $jobData)
	{
		$jobData->fileFormat = KalturaDryRunFileType::LIST_RESPONSE;
		$resultsCount = count($firstPage->objects);
		$resultObjects = $firstPage->objects;
		if($resultsCount)
		{
			$count = $resultsCount;
			$this->updateFitler($firstPage->objects);
			while($resultsCount < $this->maxResults && $count == self::PAGE_SIZE)
			{
				$results = ScheduledTaskBatchHelper::query($this->client, $this->scheduledTaskProfile, $this->pager, $this->filter);
				$count = count($results->objects);
				$resultObjects = array_merge($resultObjects, $results->objects);
				$resultsCount += $count;
			}
		}

		$this->saveResponseListResult($resultObjects, $resultsCount);
		$jobData->totalCount = $resultsCount;
	}

	private function saveResponseListResult($objects, $totalCount)
	{
		$response = new KalturaObjectListResponse();
		$response->totalCount = $totalCount;
		$response->objects = $objects;
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
		$firstPage = ScheduledTaskBatchHelper::query($this->client, $this->scheduledTaskProfile, $this->pager, $this->filter);
		if($firstPage->totalCount > self::MAX_RESULTS_THRESHOLD && $this->maxResults > self::MAX_RESULTS_THRESHOLD)
		{
			$this->execDryRunInCSVMode($firstPage, $jobData);
		}
		else
		{
			$this->execDryRunInListResponseMode($firstPage, $jobData);
		}

		$this->closeDryRun($jobData);
		return $this->closeJob($job, null, null, 'Dry run finished', KalturaBatchJobStatus::FINISHED, $jobData);
	}

	private function closeDryRun($jobData)
	{
		$this->unimpersonate();
		fclose($this->handle);
		kFile::moveFile($this->tempFilePath, $this->sharedFilePath);
		KalturaLog::info('Temp shared path: '.$this->sharedFilePath);
		$jobData->resultsFilePath = $this->sharedFilePath;
	}

	/**
	 * @param KalturaBaseEntryArray $entries
	 */
	private function updateFitler($entries)
	{
		$lastResult = end($entries);
		$this->filter->createdAtGreaterThanOrEqual = $lastResult->createdAt;
		$idsToIgnore = ScheduledTaskBatchHelper::getEntriesIdWithSameCreateAtTime($entries, $lastResult->createdAt);
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
