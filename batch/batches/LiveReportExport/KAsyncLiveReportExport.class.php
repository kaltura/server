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
		return $this->createCsv($job, $job->data);
	}

	protected function createCsv(KalturaBatchJob $job, KalturaLiveReportExportJobData $data) {
		$type = $job->jobSubType;
		$exporter = LiveReportFactory::getExporter($type);
		$exporter->run();
	}
}
