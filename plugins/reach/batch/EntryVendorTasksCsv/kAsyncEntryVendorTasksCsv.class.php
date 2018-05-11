<?php
/**
 * @package Reach
 * @subpackage Batch
 */

/**
 * Will create csv of entry vendor tasks and mail it
 *
 * @package Reach
 * @subpackage EntryVendorTasks-Csv
 */
class KAsyncEntryVendorTasksCsv extends KJobHandlerWorker
{
	private $apiError = null;

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::ENTRY_VENDOR_TASK_CSV;
	}

	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	protected function getJobType()
	{
		return KalturaBatchJobType::ENTRY_VENDOR_TASK_CSV;
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->generateEntryVendorTasksCsv($job, $job->data);
	}

	/**
	 * Generate csv contains Entry Vendor Tasks info which will be later sent by mail
	 */
	private function generateEntryVendorTasksCsv(KalturaBatchJob $job, KalturaEntryVendorTaskCsvJobData $data)
	{
		$this->updateJob($job, "Start generating Entry Vendor Tasks csv", KalturaBatchJobStatus::PROCESSING);
		self::impersonate($job->partnerId);

		// Create local path for csv generation
		$directory = self::$taskConfig->params->localTempPath . DIRECTORY_SEPARATOR . $job->partnerId;
		KBatchBase::createDir($directory);
		$filePath = $directory . DIRECTORY_SEPARATOR . 'reachTasks_' . date("Ymd") . '.csv';
		$data->outputPath = $filePath;
		KalturaLog::info("Temp file path: [$filePath]");

		//fill the csv with users data
		$csvFile = fopen($filePath, "w");
		$this->fillEntryVendorTasksCsv($csvFile, $data);
		fclose($csvFile);
		$this->setFilePermissions($filePath);
		self::unimpersonate();

		if ($this->apiError)
		{
			$e = $this->apiError;
			return $this->closeJob($job, KalturaBatchJobErrorTypes::KALTURA_API, $e->getCode(), $e->getMessage(), KalturaBatchJobStatus::RETRY);
		}

		// Copy the report to shared location.
		$this->moveFile($job, $data, $job->partnerId);
		return $job;
	}


	/**
	 * the function move the file to the shared location
	 */
	protected function moveFile(KalturaBatchJob $job, KalturaEntryVendorTaskCsvJobData $data, $partnerId)
	{
		$fileName = basename($data->outputPath);
		$directory = self::$taskConfig->params->sharedTempPath . DIRECTORY_SEPARATOR . $partnerId . DIRECTORY_SEPARATOR;
		KBatchBase::createDir($directory);
		$sharedLocation = $directory . $fileName;

		$fileSize = kFile::fileSize($data->outputPath);
		rename($data->outputPath, $sharedLocation);
		$data->outputPath = $sharedLocation;

		$this->setFilePermissions($sharedLocation);
		if (!$this->checkFileExists($sharedLocation, $fileSize))
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'Failed to move users csv file', KalturaBatchJobStatus::RETRY);
		}

		return $this->closeJob($job, null, null, 'entry Vendor Tasks CSV created successfully', KalturaBatchJobStatus::FINISHED, $data);
	}

	/**
	 * The function fills the csv file with the users data
	 */
	private function fillEntryVendorTasksCsv(&$csvFile, &$data)
	{
		$filter = clone $data->filter;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;

		$this->addHeaderRowToCsv($csvFile);
		$lastCreatedAt = 0;
		$totalCount = 0;
		$filter->orderBy = KalturaEntryVendorTaskOrderBy::CREATED_AT_ASC;
		do
		{
			if ($lastCreatedAt)
			{
				$filter->createdAtGreaterThanOrEqual = $lastCreatedAt;
			}
			try
			{
				$entryVendorTaskList = KBatchBase::$kClient->entryVendorTask->listAction($filter, $pager);
				$returnedSize = count($entryVendorTaskList->objects);
			}
			catch (Exception $e)
			{
				KalturaLog::info("Couldn't list entry Vendor Tasks on page: [$pager->pageIndex]" . $e->getMessage());
				$this->apiError = $e;
				return;
			}

			$this->addEntryVendorTasksToCsv($entryVendorTaskList->objects, $csvFile);
			$tasksCount = count($entryVendorTaskList->objects);
			$totalCount += $tasksCount;
			KalturaLog::debug("Adding More - $tasksCount totalCount - " . $totalCount);
			unset($entryVendorTaskList);
			if (function_exists('gc_collect_cycles')) // php 5.3 and above
				gc_collect_cycles();
		} while ($pager->pageSize == $returnedSize);
	}


	/**
	 * Generate the first csv row containing the fields
	 */
	private function addHeaderRowToCsv($csvFile)
	{
		$headerRow = 'Task id,vendorPartnerId,createdAt,updatedAt,queueTime,finishTime,entryId,status,reachProfileId,catalogItemId,price,userId,moderatingUser,errDescription,notes,version,context,accuracy,outputObjectId,partnerData';
		fputcsv($csvFile, explode(',', $headerRow));
		return $csvFile;
	}


	/**
	 * The function grabs all the fields values for each EntryVendorTasks and adding them as a new row to the csv file
	 */
	private function addEntryVendorTasksToCsv(&$entryVendorTasks, &$csvFile)
	{
		if (!$entryVendorTasks)
			return;

		$entryVendorTasksIds = array();
		$entryVendorTaskIdToRow = array();

		foreach ($entryVendorTasks as $entryVendorTask)
		{
			$entryVendorTasksIds[] = $entryVendorTask->id;
			$entryVendorTaskIdToRow = $this->initializeCsvRowValues($entryVendorTask, $entryVendorTaskIdToRow);
		}

		foreach ($entryVendorTaskIdToRow as $key => $val)
			fputcsv($csvFile, $val);
	}

	/**
	 * adds the default fields values and the additional fields as nulls
	 */
	private function initializeCsvRowValues($entryVendorTask, $entryVendorTaskIdToRow)
	{
		$defaultRowValues = array(
			'Task id' => $entryVendorTask->id,
			'vendorPartnerId' => $entryVendorTask->vendorPartnerId,
			'createdAt' => $entryVendorTask->createdAt,
			'updatedAt' => $entryVendorTask->updatedAt,
			'queueTime' => $entryVendorTask->queueTime,
			'finishTime' => $entryVendorTask->finishTime,
			'entryId' => $entryVendorTask->entryId,
			'status' => $entryVendorTask->status,
			'reachProfileId' => $entryVendorTask->reachProfileId,
			'catalogItemId' => $entryVendorTask->catalogItemId,
			'price' => $entryVendorTask->price,
			'userId' => $entryVendorTask->userId,
			'moderatingUser' => $entryVendorTask->moderatingUser,
			'errDescription' => $entryVendorTask->errDescription,
			'notes' => $entryVendorTask->notes,
			'version' => $entryVendorTask->version,
			'context' => $entryVendorTask->context,
			'accuracy' => $entryVendorTask->accuracy,
			'outputObjectId' => $entryVendorTask->outputObjectId,
			'partnerData' => $entryVendorTask->partnerData
		);

		$entryVendorTaskIdToRow[$entryVendorTask->id] = $defaultRowValues;

		return $entryVendorTaskIdToRow;
	}
}