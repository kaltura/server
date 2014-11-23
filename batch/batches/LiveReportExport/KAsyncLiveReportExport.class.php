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
		$this->closeJob($job, null, null, 'CSV created successfully', KalturaBatchJobStatus::FINISHED);
		return $job;
	}

	protected function createCsv(KalturaBatchJob $job, KalturaLiveReportExportJobData $data) {
		$type = $job->jobSubType;
		$exporter = LiveReportFactory::getExporter($job->partnerId, $type, $data);
		$exporter->run();
		return $job;
	}
}
