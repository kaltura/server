<?php

class EntryTimeLineLiveExporter extends LiveReportEntryExporter {

	public function __construct($partnerId, KalturaLiveReportExportJobData $data) {
		parent::__construct($partnerId, $data);
		
		$this->params[LiveReportConstants::IS_LIVE] = true;
		
		$fromTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference - LiveReportConstants::SECONDS_36_HOURS);
		$toTime = date(LiveReportConstants::DATE_FORMAT, $data->timeReference);
		$this->fileName = $data->outputPath . DIRECTORY_SEPARATOR . "audience-%s-%s.csv";
		$data->outputPath =  $this->fileName;
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

}
