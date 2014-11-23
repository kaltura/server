<?php

class EntryTimeLineAllExporter extends LiveReportEntryExporter {

	public function __construct($partnerId, KalturaLiveReportExportJobData $data) {
		parent::__construct($partnerId, $data);
		
		$fromTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference - LiveReportConstants::SECONDS_36_HOURS);
		$toTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference);
		$this->fileName = $data->outputPath . DIRECTORY_SEPARATOR . "audience-%s-%s.csv";
		$data->outputPath =  $this->fileName;
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
