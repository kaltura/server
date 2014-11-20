<?php

class LocationLiveExporter extends LiveReportEntryExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data);
		
		$this->params[LiveReportConstants::IS_LIVE] = true;
		
		$fromTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference - LiveReportConstants::SECONDS_36_HOURS);
		$toTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference);
		$this->fileName = "location-live-now-%s-%s.csv";
	}

	protected function getEngines() {
		return array_merge(
			array(
					new LiveReportConstantStringEngine("Report Type: Referrers of pure live (%s)", array("ENTRY_IDS")),
					new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
					new LiveReportConstantStringEngine("Time Range: %s - %s", array("TIME_REFERENCE_PARAM", "TIME_REFERENCE_PARAM"))),
			$this->liveEntriesEngines,
			array(new LiveReportLocation1MinEngine())
		);
	}
}
