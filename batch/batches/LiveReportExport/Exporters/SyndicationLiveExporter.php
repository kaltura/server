<?php

class SyndicationLiveExporter extends LiveReportEntryExporter {

	public function __construct($timeReference) {
		$fromTime = date(LiveReportConstants::DATE_FORMAT, $timeReference - LiveReportConstants::SECONDS_36_HOURS);
		$toTime = date(LiveReportConstants::DATE_FORMAT, $timeReference);
		$this->fileName = "referrers-live-now-%s-%s.csv";
	}

	protected function getEngines() {
		return array_merge(
			array(
					new LiveReportConstantStringEngine("Report Type: Referrers of pure live (%s)"),
					new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
					new LiveReportConstantStringEngine("Time Range: %s - %s")),
			$this->liveEntriesEngines,
			array(new LiveReportReferrerEngine())
		);

	}
	
	public function init(KalturaLiveReportExportJobData $jobData) {
		parent::init($jobData);
		$this->params[LiveReportConstants::IS_LIVE] = true;
	}
}
