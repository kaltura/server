<?php

class EntryTimeLineLiveExporter extends LiveReportEntryExporter {

	public function __construct($timeReference) {
		$fromTime = date(LiveReportConstants::DATE_FORMAT, $timeReference - LiveReportConstants::SECONDS_36_HOURS);
		$toTime = date(LiveReportConstants::DATE_FORMAT, $timeReference);
		$this->fileName = "audience-%s-%s.csv";
	}
	
	protected function getEngines() {
		$audienceLiveReport = array_merge(
			array(
					new LiveReportConstantStringEngine("Report Type: Audience of pure live (%s)"),
					new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
					new LiveReportConstantStringEngine("Time Range: %s - %s")),
			$this->liveEntriesEngines,
			array(new LiveReportAudienceEngine())
		);
		return $audienceLiveReport;
	}

	public function init(KalturaLiveReportExportJobData $jobData) {
		parent::init($jobData);
		$this->params[LiveReportConstants::IS_LIVE] = true;
	}
}
