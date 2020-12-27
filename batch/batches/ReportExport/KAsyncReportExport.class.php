<?php
/**
 * @package Scheduler
 * @subpackage ReportExport
 */
class KAsyncReportExport extends KJobHandlerWorker
{

	public static function getType()
	{
		return KalturaBatchJobType::REPORT_EXPORT;
	}

	/**
	 * @param KalturaBatchJob $job
	 * @return KalturaBatchJob
	 */
	protected function exec(KalturaBatchJob $job)
	{
		$this->updateJob($job, 'Creating CSV Export', KalturaBatchJobStatus::PROCESSING);
		$job = $this->createCsv($job, $job->data);
		return $job;
	}

	protected function createCsv(KalturaBatchJob $job, KalturaReportExportJobData $data)
	{
		$partnerId = $job->partnerId;

		$outputDir = self::$taskConfig->params->localTempPath . DIRECTORY_SEPARATOR . $partnerId;
		KBatchBase::createDir($outputDir);

		$reportFiles = array();

		$reportItems = $data->reportItems;
		foreach ($reportItems as $reportItem)
		{
			$engine = ReportExportFactory::getEngine($reportItem, $outputDir);
			if (!$engine)
			{
				return $this->closeJob($job, null, null, 'Report export engine not found', KalturaBatchJobStatus::FAILED, $data);
			}

			try
			{
				KBatchBase::impersonate($job->partnerId);
				$reportFile = $engine->createReport();
				$reportEmailName = $engine->getEmailFileName();
				KBatchBase::unimpersonate();
				$exportFile = new KalturaReportExportFile();
				$exportFile->fileId = $reportFile;
				$exportFile->fileName = $reportEmailName;
				$reportFiles[] = $exportFile;
				$this->setFilePermissions($reportFile);
			}
			catch (Exception $e)
			{
				KBatchBase::unimpersonate();
				return $this->closeJob($job, null, null, 'Cannot create report', KalturaBatchJobStatus::RETRY, $data);
			}
		}

		$this->moveFiles($reportFiles, $job, $data, $partnerId);
		return $job;
	}

	protected function moveFiles($tmpFiles, KalturaBatchJob $job, KalturaReportExportJobData $data, $partnerId)
	{
		KBatchBase::createDir(self::$taskConfig->params->sharedTempPath. DIRECTORY_SEPARATOR . $partnerId);
		$outFiles = array();
		foreach ($tmpFiles as $tmpFile)
		{
			$res = $this->moveFile($tmpFile->fileId, $partnerId);
			if (!$res)
			{
				return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'Failed to move report file', KalturaBatchJobStatus::RETRY);
			}
			$exportFile = new KalturaReportExportFile();
			$exportFile->fileId = $res;
			$exportFile->fileName = $tmpFile->fileName;

			$outFiles[] = $exportFile;
		}

		$data->files = $outFiles;
		return $this->closeJob($job, null, null, 'CSV files created successfully', KalturaBatchJobStatus::FINISHED, $data);
	}

	protected function moveFile($filePath, $partnerId)
	{
		$fileName =  basename($filePath);
		$sharedLocation = self::$taskConfig->params->sharedTempPath . DIRECTORY_SEPARATOR . $partnerId . DIRECTORY_SEPARATOR . $partnerId . "_" . $fileName;

		$fileSize = kFile::fileSize($filePath);
		rename($filePath, $sharedLocation);

		$this->setFilePermissions($sharedLocation);
		if (!$this->checkFileExists($sharedLocation, $fileSize))
		{
			return false;
		}
		return $sharedLocation;
	}

}
