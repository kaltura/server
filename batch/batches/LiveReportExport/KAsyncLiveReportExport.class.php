<?php
/**
 * @package Scheduler
 * @subpackage LiveReportExport
 */
class KAsyncLiveReportExport  extends KJobHandlerWorker
{

	public static function getType()
	{
		return KalturaBatchJobType::LIVE_REPORT_EXPORT;
	}

	protected function exec(KalturaBatchJob $job)
	{
		$this->updateJob($job, 'Creating CSV Export', KalturaBatchJobStatus::QUEUED);
		$job = $this->createCsv($job, $job->data);
		return $job;
	}

	protected function createCsv(KalturaBatchJob $job, KalturaLiveReportExportJobData $data) {
		$partnerId =  $job->partnerId;
		$type = $job->jobSubType;
		
		// Create local path for report generation
		$data->outputPath = self::$taskConfig->params->localTempPath . DIRECTORY_SEPARATOR . $partnerId;
		KBatchBase::createDir($data->outputPath);
		
		// Generate report
		KBatchBase::impersonate($job->partnerId);
		$exporter = LiveReportFactory::getExporter($type, $data);
		$reportFile = $exporter->run();
		$this->setFilePermissions($reportFile);
		KBatchBase::unimpersonate();
		
		// Copy the report to shared location.
		$this->moveFile($job, $data, $partnerId);
		
		return $job;
	}
	
	protected function moveFile(KalturaBatchJob $job, KalturaLiveReportExportJobData $data, $partnerId) {
		$fileName =  basename($data->outputPath);
		$sharedLocation = self::$taskConfig->params->sharedPath . DIRECTORY_SEPARATOR . $partnerId . "_" . $fileName;
		
		$fileSize = kFile::fileSize($data->outputPath);
		rename($data->outputPath, $sharedLocation);
		$data->outputPath = $sharedLocation;
		
		$this->setFilePermissions($sharedLocation);
		if(!$this->checkFileExists($sharedLocation, $fileSize))
		{
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::NFS_FILE_DOESNT_EXIST, 'Failed to move report file', KalturaBatchJobStatus::RETRY);
		}
	
		return $this->closeJob($job, null, null, 'CSV created successfully', KalturaBatchJobStatus::FINISHED, $data);
	}
	
}
