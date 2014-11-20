<?php

class EntryTimeLineAllExporter extends LiveReportEntryExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data);
		
		$fromTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference - LiveReportConstants::SECONDS_36_HOURS);
		$toTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference);
		$this->fileName = "audience-%s-%s.csv";
	}
	
	protected function getEngines() {
		$audienceAllReport = array_merge(
			array(
					new LiveReportConstantStringEngine("Report Type: Audience of pure live (%s)"),
					new LiveReportConstantStringEngine(LiveReportConstants::ROWS_SEPARATOR),
					new LiveReportConstantStringEngine("Time Range: %s - %s")),
			$this->allEntriesEngines,
			array(new LiveReportAudienceEngine())
		);
		return $audienceAllReport;
	}

}
