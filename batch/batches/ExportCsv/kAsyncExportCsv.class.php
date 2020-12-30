<?php
/**
 * @package Scheduler
 * @subpackage Copy
 */

/**
 * Will create csv of objects and mail it
 *
 * @package Scheduler
 * @subpackage Export-Csv
 */
class KAsyncExportCsv extends KJobHandlerWorker
{

	private $apiError = null;

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::EXPORT_CSV;
	}
	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	protected function getJobType()
	{
		return KalturaBatchJobType::EXPORT_CSV;
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->generateCsvForExport($job, $job->data);
	}

	/**
	 * Generate csv contains users info which will be later sent by mail
	 */
	private function generateCsvForExport(KalturaBatchJob $job, KalturaExportCsvJobData $data)
	{
		$this->updateJob($job, "Start generating csv for export", KalturaBatchJobStatus::PROCESSING);
		self::impersonate($job->partnerId);

		// Create local path for csv generation
		$directory = self::$taskConfig->params->localTempPath . DIRECTORY_SEPARATOR . $job->partnerId;
		KBatchBase::createDir($directory);
		$filePath = $directory . DIRECTORY_SEPARATOR . 'export_' .$job->partnerId.'_'.$job->id . '.csv';
		$data->outputPath = $filePath;
		KalturaLog::info("Temp file path: [$filePath]");

		//fill the csv with users data
		$csvFile = fopen($filePath,"w");
		
		$engine = KObjectExportEngine::getInstance($job->jobSubType);
		$engine->fillCsv($csvFile, $data);
		
		fclose($csvFile);
		$this->setFilePermissions($filePath);
		self::unimpersonate();

		if($this->apiError)
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
	protected function moveFile(KalturaBatchJob $job, KalturaExportCsvJobData $data, $partnerId)
	{
		$sharedFile = $data->storageDestinationFilePath;
		$fileName = basename($data->outputPath);
		if (!$sharedFile)
		{
			$directory = self::$taskConfig->params->sharedTempPath . DIRECTORY_SEPARATOR . $partnerId . DIRECTORY_SEPARATOR;
			KBatchBase::createDir($directory);
			$sharedLocation = $directory . $fileName;

			$fileSize = kFile::fileSize($data->outputPath);
			rename($data->outputPath, $sharedLocation);
			$data->outputPath = $sharedLocation;

			$this->setFilePermissions($sharedLocation);
			if (!$this->checkFileExists($sharedLocation, $fileSize))
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'Failed to move csv file', KalturaBatchJobStatus::RETRY);
			}
		}
		else
		{
			$sharedLocation = $sharedFile . DIRECTORY_SEPARATOR . $fileName;
			$fileSize = kFile::fileSize($data->outputPath);
			kFile::moveFile($data->outputPath, $sharedLocation);
			if(!kFile::checkFileExists($sharedLocation) || (kFile::isFile($sharedLocation) && kFile::fileSize($sharedLocation) != $fileSize))
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::OUTPUT_FILE_DOESNT_EXIST, 'Failed to move csv file', KalturaBatchJobStatus::RETRY);
			}
		}
		return $this->closeJob($job, null, null, 'CSV created successfully', KalturaBatchJobStatus::FINISHED, $data);
	}

}

